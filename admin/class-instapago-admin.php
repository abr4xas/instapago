<?php

use Automattic\WooCommerce\Utilities\FeaturesUtil;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Instapago
 * @subpackage Instapago/admin
 * @author     Angel Cruz <hello@tepuilabs.dev>
 */
class Instapago_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    8.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    8.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private string $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name  The name of this plugin.
	 * @param  string  $version    The version of this plugin.
	 *
	 *@since    8.0.0
	 */
	public function __construct( string $plugin_name, string $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    8.0.0
	 */
	public function enqueue_styles(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Instapago_Loader as all the hooks are defined
		 * in that particular class.
		 *
		 * The Instapago_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/instapago-admin.css',
            [],
            $this->version,
            'all'
        );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    8.0.0
	 */
	public function enqueue_scripts(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Instapago_Loader as all the hooks are defined
		 * in that particular class.
		 *
		 * The Instapago_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/instapago-admin.js',
            ['jquery'],
            $this->version,
            false
        );
	}

	/**
	 * Undocumented function
	 *
	 * @param  array  $links
	 *
	 * @return array|string[]
	 */
	public function instapago_action_links( array $links ): array {
		$plugin_links = [
			'<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=instapago') . '">' .
            __('Settings', 'instapago') . '</a>',
		];

		// Merge our new link with the default ones
		return array_merge($plugin_links, $links);
	}

	/**
	 * Add the gateway to WC Available Gateways.
	 *
	 * @param array $methods all available WC gateways
	 *
	 * @return string[] $methods all WC gateways + WC_Gateway_Instapago_Commerce
	 *@since 8.0.0
	 *
	 */
	public function add_instapago_class(array $methods): array {
		$methods[] = 'WC_Gateway_Instapago_Commerce';

		return $methods;
	}

	public function init_instapago_bank_class(): WC_Gateway_Instapago_Commerce {

		require_once plugin_dir_path(dirname(__FILE__)) . 'payment/WC_Gateway_Instapago_Commerce.php';

		return new WC_Gateway_Instapago_Commerce();
	}

	public function custom_admin_notices(): void {
		if (!get_option('instapago_keyid') || !get_option('instapago_public_keyid')) {
			echo '<div class="notice notice-error">
			<p>Los parámetros "keyId" y "publicKeyId" son requeridos para poder iniciar a usar instapago.</p>
			</div>';
		}
	}

	public function add_instapago_settings_page(): void {
		add_menu_page(
			__('Instapago Settings', 'instapago'), // Título de la página
			__('Instapago ', 'instapago'), // Texto del menú
			'manage_options', // Capacidad necesaria para acceder a la página
			'instapago-settings', // Slug de la página
			[$this, 'show_instapago_settings_page'], // Función para mostrar la página
			plugins_url('instapago/admin/img/icon-20x20.png'), // Icono
		);
	}

	public function show_instapago_settings_page(): void {

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/instapago-settings.php';
	}

	public function instapago_settings_notice(): void {

		if (
			isset($_GET['page'])
			&& 'instapago-settings' == $_GET['page']
			&& isset($_GET['settings-updated'])
            && $_GET['settings-updated']
		) {
			echo '
			<div class="notice notice-success is-dismissible">
				<p>
					<strong>Instapago settings saved.</strong>
				</p>
			</div>
			';
		}
	}

	public function instapago_settings_fields(): void {
		// I created variables to make the things clearer
		$page_slug = 'instapago-settings';
		$option_group = 'instapago_settings';
		//
		add_settings_section(
			'instapago_apikeys', // section ID
			'', // title (optional)
			'', // callback function to display the section (optional)
			$page_slug
		);

		register_setting(
			$option_group,
			'instapago_keyid',
		);

		register_setting(
			$option_group,
			'instapago_public_keyid',
		);

		add_settings_field(
			'instapago_keyid',
			'Key ID: ',
			[$this, 'input_text'], // function to print the field
			$page_slug,
			'instapago_apikeys',
			[
				'label_for' => 'instapago_keyid',
				'class' => 'hello', // for <tr> element
				'name' => 'instapago_keyid', // pass any custom parameters
				'type' => 'text', // text, textarea, select, checkbox, radio
				'value' => get_option('instapago_keyid')
			]
		);

		add_settings_field(
			'instapago_public_keyid',
			'Public Key ID: ',
			[$this, 'input_text'], // function to print the field
			$page_slug,
			'instapago_apikeys',
			[
				'label_for' => 'instapago_public_keyid',
				'class' => 'hello', // for <tr> element
				'name' => 'instapago_public_keyid', // pass any custom parameters
				'type' => 'text', // text, textarea, select, checkbox, radio
				'value' => get_option('instapago_public_keyid')
			]
		);
	}

	// custom callback function to print field HTML
	public function input_text($args): void {
		echo '<input type="'. $args['type'] .'" id="'. $args['name'] . '" class="' . $args['name'] . '" name="'. $args['name'] .'" value="'. $args['value'] .'" />';
	}

    public function instapago_gateway_cart_checkout_blocks_compatibility(): void {

        $path =  WP_PLUGIN_DIR.'/instapago';
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            FeaturesUtil::declare_compatibility(
                'cart_checkout_blocks',
                $path,
                true // true (compatible, default) or false (not compatible)
            );

            FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                $path,
                true
            );
        }

    }

    public function instapago_gateway_block_support(): void {

        // here we're including our "gateway block support class"
        require_once WP_PLUGIN_DIR.'/instapago/support/class-wc-instapago-gateway-blocks-support.php';

        // registering the PHP class we have just included
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
                $payment_method_registry->register( new WC_Instapago_Gateway_Blocks_Support );
            }
        );

    }
}
