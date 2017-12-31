<?php
/**
 * Plugin Name: Instapago Payment Gateway for WooCommerce
 * Plugin URI: https://www.behance.net/gallery/37073215/Instapago-Payment-Gateway-for-WooCommerce
 * Description: Instapago is a technological solution designed for the market of electronic commerce (eCommerce) in Venezuela and Latin America, with the intention of offering a premium product category, which allows people and companies leverage their expansion capabilities, facilitating payment mechanisms for customers with a friendly integration into systems currently used.
 * Text Domain: instapago
 * Version: 3.0.0
 * Author: Angel Cruz
 * Author URI: http://abr4xas.org
 * Requires at least: 4.8
 * Tested up to: 4.9.1
 *
 * @category Admin
 *
 * @author Angel Cruz
 * @copyright Copyright (C) Angel Cruz <me@abr4xas.org> and WooCommerce
 */
if (!defined('ABSPATH')) {
    exit;
}

require 'vendor/autoload.php';

use \Instapago\Api;

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}
// Register style
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );

function register_plugin_styles() {
  wp_register_style( 'instapago-plugin-styles', plugins_url( 'assets/css/style.css', __FILE__ ) );
  // Hay que prograpar la funcion localize script para que funcione correctamente
  //wp_register_script( 'instapago-plugin-script', plugins_url( 'assets/js/main.js', __FILE__ ) );

  wp_enqueue_style( 'instapago-plugin-styles' );
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
    $methods[] = 'WC_Gateway_Instapago_Commerce';

    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_instapago_class');

// PHPMailer Class from WP core
include_once ABSPATH.WPINC.'/class-phpmailer.php';

/*
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
        public $version = '3.0.0';

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

            $this->msg['message']   = '';
            $this->msg['class']     = '';
            //Save hook
            add_action('woocommerce_update_options_payment_gateways_'.$this->id, [$this, 'process_admin_options']);
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
            $order          = wc_get_order($order_id);
            $cardHolder     = strip_tags(trim($_POST['card_holder_name']));
            $cardHolderId   = strip_tags(trim($_POST['user_dni']));
            $cardNumber     = strip_tags(trim($_POST['valid_card_number']));
            $cvc            = strip_tags(trim($_POST['cvc_code']));
            $exp_month      = strip_tags(trim($_POST['exp_month']));
            $exp_year       = strip_tags(trim($_POST['exp_year']));
            $expirationDate = $exp_month.'/'.$exp_year;

            $paymentData = [
                'amount'            => $order->get_total(),
                'description'       => 'Generating payment for order #'.$order->get_order_number(),
                'card_holder'       => $cardHolder,
                'card_holder_id'    => $cardHolderId,
                'card_number'       => $cardNumber,
                'cvc'               => $cvc,
                'expiration'        => $expirationDate,
                'ip'                => $_SERVER['REMOTE_ADDR'],
            ];
            try{

                $api = new Api($this->keyId,$this->publicKeyId);

                if ($this->paymod == 1) {
                    $respuesta = $api->reservePayment($paymentData);
                } else {
                    $respuesta = $api->directPayment($paymentData);
                }
                
                $order->payment_complete();
                $order->add_order_note(__('Mensaje del Banco:<br/> <strong>'.$respuesta['msg_banco'].'</strong><br/> Número de Identificación del Pago:<br/><strong>'.$respuesta['id_pago'].'</strong><br/>Referencia Bancaria: <br/><strong>'.$respuesta['reference'].'</strong>', 'woothemes'));
    
                if ($this->debug == 'yes') {
                    $logger = wc_get_logger();
    
                    $context = [
                        'source' => 'instapago',
                    ];
                    $logger->log('info', 'Se ha procesado un pago', $context);
                    file_put_contents(dirname(__FILE__).'/data.log', print_r($respuesta, true)."\n\n".'======================'."\n\n", FILE_APPEND | LOCK_EX);
                }
    
                update_post_meta($order_id, 'instapago_voucher', $respuesta['voucher']);
                update_post_meta($order_id, 'instapago_bank_ref', $respuesta['reference']);
                update_post_meta($order_id, 'instapago_id_payment', $respuesta['id_pago']);
                update_post_meta($order_id, 'instapago_bank_msg', $respuesta['msg_banco']);
    
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
            }catch(\Instapago\Exceptions\InstapagoException $e){
                throw new \Exception($e->getMessage()); // manejar el error
            }catch(\Instapago\Exceptions\AuthException $e){
                throw new \Exception($e->getMessage()); // manejar el error
            }catch(\Instapago\Exceptions\BankRejectException $e){
                throw new \Exception($e->getMessage()); // manejar el error
            }catch(\Instapago\Exceptions\InvalidInputException $e){
                throw new \Exception($e->getMessage()); // manejar el error
            }catch(\Instapago\Exceptions\TimeoutException $e){
                throw new \Exception($e->getMessage()); // manejar el error
            }catch(\Instapago\Exceptions\ValidationException $e){
                throw new \Exception($e->getMessage()); // manejar el error
            }
        }
    } // End WC_Gateway_Instapago_Commerce
} // End init_instapago_class()