<?php

/**
 * Plugin Name: Instapago Payment Gateway for WooCommerce
 * Plugin URI: https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
 * Description: Instapago is a technological solution designed for the market of electronic commerce (eCommerce) in Venezuela and Latin America, with the intention of offering a premium product category, which allows people and companies leverage their expansion capabilities, facilitating payment mechanisms for customers with a friendly integration into systems currently used.
 * Text Domain: instapago
 * Version: 6.0.0
 * Author: Angel Cruz
 * Author URI: http://abr4xas.org
 * Requires at least: 5.5
 * Tested up to: 5.5
 *
 * @category Admin
 *
 * @author Angel Cruz
 * @copyright Copyright (C) Angel Cruz <bullgram@gmail.com> and WooCommerce
 */
if (!defined('ABSPATH')) {
    exit;
}

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}
// Register style
add_action('wp_enqueue_scripts', 'register_plugin_styles');

function register_plugin_styles()
{
    wp_register_style('instapago-plugin-styles', plugins_url('assets/css/style.css', __FILE__));
    // Hay que prograpar la funcion localize script para que funcione correctamente
    //wp_register_script( 'instapago-plugin-script', plugins_url( 'assets/js/main.js', __FILE__ ) );

    wp_enqueue_style('instapago-plugin-styles');
    //wp_enqueue_script( 'instapago-plugin-script' );
}

// Add custom action links
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'instapago_action_links');

function instapago_action_links($links)
{
    $plugin_links = [
        '<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout&section=instapago').'">'.__('Settings', 'instapago').'</a>',
    ];

    // Merge our new link with the default ones
    return array_merge($plugin_links, $links);
}
/**
 * Add the gateway to WC Available Gateways.
 *
 * @since 1.0.0
 *
 * @param array $methods all available WC gateways
 *
 * @return string[] $methods all WC gateways + WC_Gateway_Instapago_Commerce
 */
function add_instapago_class($methods)
{
    $methods[ ] = 'WC_Gateway_Instapago_Commerce';

    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_instapago_class');

/*
 * Instapago Payment Gateway for WooCommerce
 *
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_Gateway_Instapago_Commerce
 * @extends		WC_Payment_Gateway
 * @version		5.0.1
 * @package		WooCommerce/Classes/Payment
 * @author 		Angel Cruz
 */
add_action('plugins_loaded', 'init_instapago_class', 11);

function init_instapago_class()
{
    class WC_Gateway_Instapago_Commerce extends WC_Payment_Gateway
    {
        /**
         * Constructor for the gateway.
         */
        public function __construct()
        {
            global $woocommerce;
            $this->id = 'instapago';
            $this->order_button_text    = __('Pagar con Instapago', 'woocommerce');
            $this->medthod_title        = __('Instapago', 'woocommerce');
            $this->method_description   = sprintf(__('Es una solución tecnológica pensada para el mercado de comercio electrónico (eCommerce) en Venezuela y Latinoamérica, con la intención de ofrecer un producto de primera categoría, que permita a las personas y empresas apalancar sus capacidades de expansión, facilitando los mecanismos de pago para sus clientes, con una integración amigable a los sistemas que actualmente utilizan.', 'woocommerce'));
            $this->has_fields = true;
            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();
            // Define user set variables.
            $this->title        = $this->get_option('title');
            $this->description  = $this->get_option('description');
            $this->keyId        = $this->get_option('api_keyId');
            $this->publicKeyId  = $this->get_option('api_publicKeyId');
            $this->debug        = $this->get_option('debug', 'yes');
            $this->paymod       = $this->get_option('paymentaction');
            // Define custom message for email
            $this->headerMail       = $this->get_option('mail_header');
            $this->subheaderMail    = $this->get_option('mail_subheader');

            $this->msg[ 'message' ]   = '';
            $this->msg[ 'class' ]     = '';
            //Save hook
            add_action('woocommerce_update_options_payment_gateways_'.$this->id, [ $this, 'process_admin_options' ]);
            add_action('woocommerce_receipt_lamdaprocessing', [ &$this, 'finalize_order' ], 0);
            add_action('woocommerce_receipt_lamdaprocessing', [ &$this, 'receipt_page' ]);
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
            $icon_html = '<img src="'.plugins_url('instapago/assets/images/instapago-gateway.png').'" alt="Instapago">';

            return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
        }

        public function payment_fields()
        {
            if ($this->debug == 'yes') {
                echo '<p><strong>TEST MODE ENABLED</strong></p>';
            }

            echo '<p>'.$this->description.'</p>';

            include 'includes/payment-fields.php';
        }

        public function process_payment($order_id)
        {
            global $woocommerce;
            $url            = 'https://api.instapago.com/payment';
            $order          = wc_get_order($order_id);
            $cardHolder     = strip_tags(trim($_POST[ 'card_holder_name' ]));
            $cardHolderId   = strip_tags(trim($_POST[ 'user_dni' ]));
            $cardNumber     = strip_tags(trim($_POST[ 'valid_card_number' ]));
            $cvc            = strip_tags(trim($_POST[ 'cvc_code' ]));
            $exp_month      = strip_tags(trim($_POST[ 'exp_month' ]));
            $exp_year       = strip_tags(trim($_POST[ 'exp_year' ]));
            $expirationDate = $exp_month.'/'.$exp_year;

            $fields = [
                'KeyID'          => $this->keyId, //required
                'PublicKeyId'    => $this->publicKeyId, //required
                'Amount'         => $order->get_total(), //required
                'Description'    => 'Generating payment for order #'.$order->get_order_number(), //required
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
                $order->add_order_note(__('Mensaje del Banco:<br/> <strong>'.$result[ 'msg_banco' ].'</strong><br/> Número de Identificación del Pago:<br/><strong>'.$result[ 'id_pago' ].'</strong><br/>Referencia Bancaria: <br/><strong>'.$result[ 'reference' ].'</strong>', 'woothemes'));

                if ($this->debug == 'yes') {
                    $logger = wc_get_logger();
                    $context = [
                        'source' => 'instapago',
                    ];
                    $logger->log('info', 'Se ha procesado un pago', $result);
                    $logger->log('info', print_r($result, true), $context);
                    file_put_contents(dirname(__FILE__).'/data.log', print_r($result, true)."\n\n".'======================'."\n\n", FILE_APPEND | LOCK_EX);
                }

                update_post_meta($order_id, 'instapago_voucher', $result[ 'voucher' ]);
                update_post_meta($order_id, 'instapago_bank_ref', $result[ 'reference' ]);
                update_post_meta($order_id, 'instapago_id_payment', $result[ 'id_pago' ]);
                update_post_meta($order_id, 'instapago_bank_msg', $result[ 'msg_banco' ]);
                update_post_meta($order_id, 'instapago_sequence', $result[ 'sequence' ]);
                update_post_meta($order_id, 'instapago_approval', $result[ 'approval' ]);
                update_post_meta($order_id, 'instapago_lote', $result[ 'lote' ]);

                // Mark as complete
                $order->update_status('completed');

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
         * @return $obj array resultados de la transaccion
         *              https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
         */
        public function curlTransaccion($url, $fields)
        {
            $myCurl = curl_init();
            curl_setopt($myCurl, CURLOPT_URL, $url);
            curl_setopt($myCurl, CURLOPT_POST, 1);
            curl_setopt($myCurl, CURLOPT_POSTFIELDS, http_build_query($fields));
            curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($myCurl);
            curl_close($myCurl);
            $obj = json_decode($server_output);

            return $obj;
        }

        /**
         * Verifica Codigo de Estado de transaccion
         * Verifica y retornar el resultado de la transaccion.
         *
         * @param $obj datos de la consulta
         *
         * @return $result array datos de transaccion
         *                 https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
         */
        public function checkResponseCode($obj)
        {
            $code = $obj->code;
            switch ($code) {
                case 400:
                    throw new \Exception('Error al validar los datos enviados.');
                    break;
                case 401:
                    throw new \Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
                    break;
                case 403:
                    throw new \Exception('Pago Rechazado por el banco.');
                    break;
                case 500:
                    throw new \Exception('Ha Ocurrido un error interno dentro del servidor.');
                    break;
                case 503:
                    throw new \Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
                    break;
                case 201:
                    return [
                        'code'      => $code,
                        'msg_banco' => $obj->message,
                        'voucher'   => html_entity_decode($obj->voucher),
                        'id_pago'   => $obj->id,
                        'reference' => $obj->reference,
                        'sequence'  => $obj->sequence,
                        'approval'  => $obj->approval,
                        'lote'      => $obj->lote,
                    ];
                    break;
                default:
                    throw new \Exception('Error general...');
                    break;
            }
        }
    } // End WC_Gateway_Instapago_Commerce
} // End init_instapago_class()
