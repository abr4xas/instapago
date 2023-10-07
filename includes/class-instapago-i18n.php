<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      8.0.0
 * @package    Instapago
 * @subpackage Instapago/includes
 * @author     Angel Cruz <hello@tepuilabs.dev>
 */
class Instapago_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    8.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'instapago',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
