<?php
/**
 * Plugin Name: Instapago Payment Gateway for WooCommerce
 * Plugin URI: https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
 * Description: Instapago is a technological solution designed for the market of electronic commerce (eCommerce) in Venezuela and Latin America, with the intention of offering a premium product category, which allows people and companies leverage their expansion capabilities, facilitating payment mechanisms for customers with a friendly integration into systems currently used.
 * Version: 1.0.0
 * Author: Angel Cruz
 * Author URI: http://abr4xas.org
 * Requires at least: 4.6
 * Tested up to: 4.7
 *
 * @package WC_Gateway_Instapago_Commerce
 * @category Admin
 * @author Angel Cruz
 * @copyright Copyright (C) Angel Cruz <me@abr4xas.org> and WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

/**
 * Add the gateway to WC Available Gateways
 *
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + WC_Gateway_Instapago_Commerce
 */
function add_instapago_class($methods)
{
    $methods[] = 'WC_Gateway_Instapago_Commerce';
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_instapago_class');

// PHPMailer Class from WP core
include_once(ABSPATH . WPINC . '/class-phpmailer.php');

/**
 * Instapago Payment Gateway for WooCommerce
 *
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_Gateway_Instapago_Commerce
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		Angel Cruz
 */
add_action('plugins_loaded', 'init_instapago_class', 11);

function init_instapago_class()
{

    class WC_Gateway_Instapago_Commerce extends WC_Payment_Gateway
    {
        /**
        * WooCommerce version.
        *
        * @var string
        */
        public $version = '1.0.0';

        /**
        * @param bool Whether or not logging is enabled
        */
        public static $log_enabled = false;

        /**
        * @param WC_Logger Logger instance
        */
        public static $log = false;

        /**
		* Constructor for the gateway.
		*/
        public function __construct()
        {
            global $woocommerce;
            $this->id = 'instapago';
            $this->order_button_text  = __( 'Pagar con Instapago', 'woocommerce' );
            $this->medthod_title = __('Instapago', 'woocommerce');
            $this->method_description = sprintf(__('Es una solución tecnológica pensada para el mercado de comercio electrónico (eCommerce) en Venezuela y Latinoamérica, con la intención de ofrecer un producto de primera categoría, que permita a las personas y empresas apalancar sus capacidades de expansión, facilitando los mecanismos de pago para sus clientes, con una integración amigable a los sistemas que actualmente utilizan.', 'woocommerce'));
            $this->has_fields = true;
            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();
            // Define user set variables.
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->keyId = $this->get_option('api_keyId');
            $this->publicKeyId = $this->get_option('api_publicKeyId');
            $this->debug = $this->get_option('debug', 'yes');
            $this->paymod = $this->get_option('paymentaction');
            // Define custom message for email
            $this->headerMail = $this->get_option('mail_header');
            $this->subheaderMail = $this->get_option('mail_subheader');
            self::$log_enabled = $this->debug;

            $this->msg['message'] = "";
            $this->msg['class'] = "";
            //Save hook
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action('woocommerce_receipt_lamdaprocessing', array(&$this, 'finalize_order'),0);
            add_action('woocommerce_receipt_lamdaprocessing', array(&$this, 'receipt_page'));

        }
        /**
		 * Logging method.
		 * @param string $message
		 */
        public static function log($message)
        {
            if (self::$log_enabled) {
                if (empty(self::$log)) {
                    self::$log = new WC_Logger();
                }
                self::$log->add('instapago', $message);
            }
        }
        /**
		 * Admin Panel Options.
		 */
        public function admin_options()
        {
            include ('includes/admin-options.php');
        }
        /**
		 * Initialise Gateway Settings Form Fields
		 *
		 * @access public
		 * @return void
		 */
        public function init_form_fields()
        {
            $this->form_fields = include ('includes/settings-instapago.php');
        }
        /**
		 * Get gateway icon.
		 * @return string
		 */
        public function get_icon()
        {
            $icon_html = '<img src="'.plugins_url('instapago/images/instapago-gateway.png').'" alt="Instapago">';
            return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
        }
        function payment_fields()
        {
            if ($this->debug=='yes') {
                echo '<p><strong>TEST MODE ENABLED</strong></p>';
            } else {
                echo '<p>'.$this->description.'</p>';
            }
            include ('includes/payment-fields.php');
        }
        public function process_payment ($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);
            $url = 'https://api.instapago.com/payment';
            $cardHolder 	= strip_tags(trim($_POST['card_holder_name']));
            $cardHolderId 	= strip_tags(trim($_POST['user_dni']));
            $cardNumber 	= strip_tags(trim($_POST['valid_card_number']));
            $cvc 			= strip_tags(trim($_POST['cvc_code']));
            $exp_month 		= strip_tags(trim($_POST['exp_month']));
            $exp_year		= strip_tags(trim($_POST['exp_year']));
            $expirationDate = $exp_month.'/'.$exp_year;

            $fields = [
                "KeyID" => $this->keyId, //required
                "PublicKeyId" => $this->publicKeyId, //required
                "Amount" => $order->get_total() , //required
                "Description" => 'Generating payment for order #' . $order->get_order_number(), //required
                "CardHolder" =>  $cardHolder, //required
                "CardHolderId" => $cardHolderId, //required
                "CardNumber" => $cardNumber, //required
                "CVC" =>  $cvc, //required
                "ExpirationDate" => $expirationDate, //required
                "StatusId" => $this->paymod, //required
                "IP" => $_SERVER["REMOTE_ADDR"], //required
            ];

            $obj = $this->curlTransaccion($url, $fields);

            $result = $this->checkResponseCode($obj);

            if ($this->debug == 'yes') {
                $this->log( ': se ha procesado un pago ');
                file_put_contents(dirname(__FILE__).'/data.log',print_r($result, true)."\n\n".'======================'."\n\n", FILE_APPEND);
            }

            if ($result['code'] == 201) {
                // Payment received and stock has been reduced
                $order->payment_complete();
                $order->add_order_note( __('Mensaje del Banco:<br/> <strong>'.$result['msg_banco'].'</strong><br/> Número de Identificación del Pago:<br/><strong>'.$result['id_pago'].'</strong><br/>Referencia Bancaria: <br/><strong>'.$result['reference'].'</strong>' , 'woothemes') );

                if ($this->debug == 'yes') {
                    $this->log( ': se ha procesado un pago ');
                    file_put_contents(dirname(__FILE__).'/data.log',print_r($result, true)."\n\n".'======================'."\n\n", FILE_APPEND);
                }

                // Set vars
                $adminEmail = get_option( 'admin_email', '' );
                $siteUrl    = get_site_url();
                $sender     = get_bloginfo( 'name', 'display' );
                $customerEmail = $order->billing_email;
                $customerName = $order->last_name.' '.$order->first_name;
                $voucher = $result['voucher'];
                $headerMail = $this->headerMail;
                $subheaderMail = $this->subheaderMail;
                $copyfooter = '';

                update_post_meta( $order->id, 'instapago_voucher', $voucher );

                if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
                    $logoCorreo = '<a target="_blank" style="text-decoration: none;" href="'. $siteUrl .'"><img border="0" vspace="0" hspace="0" src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" title="' . get_bloginfo( 'name', 'display' ) . '" style="color: #000000;font-size: 10px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;"/></a>';
                } else {
                    $logoCorreo ='';
                }

                // Retrieve the email template required
                $message = file_get_contents('email.html', dirname(__FILE__));

                // Replace the % with the actual information
                $message = str_replace('%logo%', $logoCorreo, $message);
                $message = str_replace('%headerMail%', $headerMail, $message);
                $message = str_replace('%subheaderMail%', $subheaderMail, $message);
                $message = str_replace('%voucher%', $voucher, $message);
                $message = str_replace('%copyfooter%', $copyfooter, $message);

                // send voucher
                $wpAdmin        = $adminEmail;
                $wpBlogName     = $sender;
                $customer       = $customerEmail;
                $customSubject  = 'Recibo de tu pedido en ';
                $customMsg = $message;

                $this->SendCustomEmail($wpAdmin, $wpBlogName, $customer, $customSubject, $customMsg);

                // Mark as complete
                $order->update_status('completed');

                // Reduce stock levels
                $order->reduce_order_stock();

                // Remove cart
                $woocommerce->cart->empty_cart();

                // Return thankyou redirect
                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
            }
        }

        /**
		 * Realiza Transaccion
		 * Efectúa y retornar una respuesta a un metodo de pago.
		 * @param $url endpoint a consultar
		 * @param $fields datos para la consulta
		 * @return $obj array resultados de la transaccion
		 * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
		 */
        public function curlTransaccion($url, $fields)
        {
            $myCurl = curl_init();
            curl_setopt($myCurl, CURLOPT_URL,$url);
            curl_setopt($myCurl, CURLOPT_POST, 1);
            curl_setopt($myCurl, CURLOPT_POSTFIELDS,http_build_query($fields));
            curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($myCurl);
            curl_close ($myCurl);
            $obj = json_decode($server_output);
            return $obj;
        }
        /**
		 * Verifica Codigo de Estado de transaccion
		 * Verifica y retornar el resultado de la transaccion.
		 * @param $obj datos de la consulta
		 * @return $result array datos de transaccion
		 * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
		 */
        public function checkResponseCode($obj)
        {
            $code = $obj->code;
            $msg  = $obj->message;
            if ($code == 400) {
                throw new \Exception('Error al validar los datos enviados.');
            } elseif ($code == 401) {
                throw new \Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
            } elseif ($code == 403) {
                throw new \Exception('Pago Rechazado por el banco.');
            } elseif ($code == 500) {
                throw new \Exception('Ha Ocurrido un error interno dentro del servidor.');
            } elseif ($code == 503) {
                throw new \Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
            } elseif ($code == 201) {
                return [
                    'code' => $code,
                    'msg_banco' => $msg,
                    'voucher' => html_entity_decode($obj->voucher) ,
                    'id_pago' => $obj->id,
                    'reference' => $obj->reference
                ];
            }
        }

        public function SendCustomEmail($wpAdmin, $wpBlogName, $customer, $customSubject, $customMsg)
        {

            $adminEmail     = $wpAdmin; // root admin
            $sender         = $wpBlogName; // Site name
            $customerEmail  = $customer; // woocommerce customer email
            $msg            = $customSubject; // custom subject
            $message        = $customMsg; // custom msg

            // Setup PHPMailer
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            $mail->Host = '127.0.0.1';
            $mail->Port = 25;
            $mail->setFrom($adminEmail, $sender);
            $mail->addAddress($customerEmail);
            $mail->isHTML(true);
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Set the subject
            $mail->Subject = $msg . '' . $sender;

            //Set the message
            $mail->MsgHTML($message);

            // Send the email
            if(!$mail->Send()) {
                $this->log( 'Mailer Error: ' . $mail->ErrorInfo);
            }
        }
    } // End WC_Gateway_Instapago_Commerce
} // End init_instapago_class()