<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Instapago
 * @subpackage Instapago/public
 * @author     Angel Cruz <hello@tepuilabs.dev>
 */
class Instapago_Public {

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
	 * @param      string    $plugin_name  The name of the plugin.
	 * @param  string  $version    The version of this plugin.
	 *
	 *@since    8.0.0
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
            plugin_dir_url( __FILE__ ) . 'css/instapago-public.css',
            [],
            $this->version,
            'all'
        );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
            plugin_dir_url( __FILE__ ) . 'js/instapago-public.js',
            ['jquery'],
            $this->version,
            false
        );

	}

}
