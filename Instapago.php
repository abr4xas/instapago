<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://angelcruz.dev
 * @since             7.0.0
 * @package           Instapago
 *
 * @wordpress-plugin
 * Plugin Name:       Instapago Payment Gateway for WooCommerce
 * Plugin URI:        https://angelcruz.dev
 * Description:       Instapago is a technological solution designed for the market of electronic commerce (eCommerce) in Venezuela and Latin America, with the intention of offering a premium product category, which allows people and companies leverage their expansion capabilities, facilitating payment mechanisms for customers with a friendly integration into systems currently used.
 * Version:           7.0.0
 * Author:            Angel Cruz
 * Author URI:        https://angelcruz.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       instapago
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 7.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'INSTAPAGO_VERSION', '7.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-instapago-activator.php
 */
function activate_instapago() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-instapago-activator.php';
	Instapago_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-instapago-deactivator.php
 */
function deactivate_instapago() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-instapago-deactivator.php';
	Instapago_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_instapago' );
register_deactivation_hook( __FILE__, 'deactivate_instapago' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-instapago.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    7.0.0
 */
function run_instapago() {

	$plugin = new Instapago();
	$plugin->run();

}
run_instapago();
