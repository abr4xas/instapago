<?php

defined('WPINC') || exit;

// Make sure WooCommerce is active
if (!in_array(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	return;
}

class WC_Gateway_Instapago_Commerce extends WC_Payment_Gateway
{

	public string $medthod_title;
	private string $keyId;
	private string $publicKeyId;
	public string $debug;
	public string $paymod;

	private function load_dependencies()
	{

		require_once plugin_dir_path(dirname(__FILE__)) . 'payment/Api.php';
	}

	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{
		global $woocommerce;
		$this->id = 'instapago';
		$this->order_button_text = __('Pagar con Instapago', 'instapago');
		$this->medthod_title = __('Instapago', 'instapago');
		$this->method_description = sprintf(__('Es una solución tecnológica pensada para el mercado de comercio electrónico (eCommerce) en Venezuela y Latinoamérica, con la intención de ofrecer un producto de primera categoría, que permita a las personas y empresas apalancar sus capacidades de expansión, facilitando los mecanismos de pago para sus clientes, con una integración amigable a los sistemas que actualmente utilizan.', 'instapago'));
		$this->has_fields = true;
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');

		$this->keyId = $this->get_option('api_keyId');
		$this->publicKeyId  = $this->get_option('api_publicKeyId');
		$this->debug = $this->get_option('debug', 'yes');
		$this->paymod = $this->get_option('paymentaction');

		//Save hook
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
		add_action('woocommerce_receipt_lamdaprocessing', [&$this, 'finalize_order'], 0);
		add_action('woocommerce_receipt_lamdaprocessing', [&$this, 'receipt_page']);
	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options()
	{
		include 'includes/admin-options.php';
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 *
	 * @return void
	 */
	public function init_form_fields()
	{
		$this->form_fields = include 'includes/settings-instapago.php';
	}

	/**
	 * Get gateway icon.
	 *
	 * @return string
	 */
	public function get_icon()
	{
		$icon_html = '<img src="' . plugins_url('instapago/public/img/instapago-gateway.png') . '" alt="Instapago" class="instapago-icon">';

		return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
	}

	public function payment_fields()
	{
		if ($this->debug === 'yes') {
			echo '<p><strong>TEST MODE ENABLED</strong></p>';
		}

		echo '<p class="instapago-form--txt-help">'. $this->description.'</p>';

		include 'includes/payment-fields.php';
	}

	public function process_payment($order_id)
	{
		global $woocommerce;

		$url            = 'https://api.instapago.com/payment';
		$order          = wc_get_order($order_id);
		$cardHolder     = strip_tags(trim($_POST['card_holder_name']));
		$cardHolderId   = strip_tags(trim($_POST['user_dni']));
		$cardNumber     = strip_tags(trim($_POST['valid_card_number']));
		$cvc            = strip_tags(trim($_POST['cvc_code']));
		$exp_month      = strip_tags(trim($_POST['exp_month']));
		$exp_year       = strip_tags(trim($_POST['exp_year']));
		$expirationDate = $exp_month . '/' . $exp_year;

		$fields = [
			'KeyID'          => $this->keyId, //required
			'PublicKeyId'    => $this->publicKeyId, //required
			'Amount'         => $order->get_total(), //required
			'Description'    => 'Generating payment for order #' . $order->get_order_number(), //required
			'CardHolder'     => $cardHolder, //required
			'CardHolderId'   => $cardHolderId, //required
			'CardNumber'     => $cardNumber, //required
			'CVC'            => $cvc, //required
			'ExpirationDate' => $expirationDate, //required
			'StatusId'       => 2, //required
			'IP'             => $_SERVER['REMOTE_ADDR'], //required
		];

		$obj = $this->curlTransaccion($url, $fields);
		$result = $this->checkResponseCode($obj);

		if ($result['code'] == 201) {
			// Payment received and stock has been reduced

			$order->payment_complete();
			$order->add_order_note(__('Mensaje del Banco:<br/> <strong>' . $result['msg_banco'] . '</strong><br/> Número de Identificación del Pago:<br/><strong>' . $result['id_pago'] . '</strong><br/>Referencia Bancaria: <br/><strong>' . $result['reference'] . '</strong>', 'woothemes'));

			if ($this->debug == 'yes') {

				$logger = wc_get_logger();

				$context = [
					'source' => 'instapago',
				];

				$logger->log('debug', 'Se ha procesado un pago', $result);
				$logger->log('debug', print_r($result, true), $context);
			}

			update_post_meta($order_id, 'instapago_voucher', $result['voucher']);
			update_post_meta($order_id, 'instapago_bank_ref', $result['reference']);
			update_post_meta($order_id, 'instapago_id_payment', $result['id_pago']);
			update_post_meta($order_id, 'instapago_bank_msg', $result['msg_banco']);
			update_post_meta($order_id, 'instapago_sequence', $result['sequence']);
			update_post_meta($order_id, 'instapago_approval', $result['approval']);
			update_post_meta($order_id, 'instapago_lote', $result['lote']);

			// Reduce stock levels
			wc_reduce_stock_levels($order_id);

			// Remove cart
			WC()->cart->empty_cart();

			// Return thankyou redirect
			return [
				'result'      => 'success',
				'redirect'    => $this->get_return_url($order),
			];
		}
	}

	/**
	 * Realiza Transaccion
	 * Efectúa y retornar una respuesta a un metodo de pago.
	 *
	 * @param string $url endpoint a consultar
	 * @param $fields datos para la consulta
	 *
	 * @return $response array resultados de la transaccion
	 */
	private function curlTransaccion($url, $fields)
	{
		$args = [
			'method' => 'POST',
			'headers'  => [
				'Content-type: application/x-www-form-urlencoded'
			],
			'body' => http_build_query($fields)
		];

		$response = wp_remote_retrieve_body(wp_remote_post($url, $args));

		$response = json_decode($response, true);

		return $response;
	}

	/**
	 * Verifica Codigo de Estado de transaccion
	 * Verifica y retornar el resultado de la transaccion.
	 *
	 * @param $response datos de la consulta
	 *
	 * @return $result array datos de transaccion
	 */
	private function checkResponseCode($response)
	{
		$code = $response['code'];
		switch ($code) {
			case 400:
				wc_add_notice(__('Error al validar los datos enviados.', 'instapago'), 'error');
				break;
			case 401:
				wc_add_notice(__('Error de autenticación, ha ocurrido un error con las llaves utilizadas.', 'instapago'), 'error');
				break;
			case 403:
				wc_add_notice(__('Pago Rechazado por el banco.', 'instapago'), 'error');
				break;
			case 500:
				wc_add_notice(__('Ha Ocurrido un error interno dentro del servidor.', 'instapago'), 'error');
				break;
			case 503:
				wc_add_notice(__('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.', 'instapago'), 'error');
				break;
			case 201:
				return [
					'code'      => $code,
					'msg_banco' => $response['message'],
					'voucher'   => html_entity_decode($response['voucher']),
					'id_pago'   => $response['id'],
					'reference' => $response['reference'],
					'sequence'  => $response['sequence'],
					'approval'  => $response['approval'],
					'lote'      => $response['lote'],
				];
				break;
			default:
				wc_add_notice(__('Error general.','instapago'), 'error');
				break;
		}
	}
}
