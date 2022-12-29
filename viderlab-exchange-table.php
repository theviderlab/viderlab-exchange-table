<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://viderlab.com
 * @since             1.0.0
 * @package           ViderLab_Exchange_Table
 *
 * @wordpress-plugin
 * Plugin Name:       ViderLab Exchange Table
 * Plugin URI:        https://viderlab.com/
 * Description:       This plugin creates a table with exchange currency rates
 * Version:           1.0.0
 * Author:            ViderLab <soporte@viderlab.com>
 * Author URI:        https://viderlab.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       viderlab-exchange-table
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VIDERLAB_EXCHANGE_TABLE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-viderlab-exchange-table-activator.php
 */
function activate_viderlab_exchange_table() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-viderlab-exchange-table-activator.php';
	ViderLab_Exchange_Table_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-viderlab-exchange-table-deactivator.php
 */
function deactivate_viderlab_exchange_table() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-viderlab-exchange-table-deactivator.php';
	ViderLab_Exchange_Table_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_viderlab_exchange_table' );
register_deactivation_hook( __FILE__, 'deactivate_viderlab_exchange_table' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-viderlab-exchange-table.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_viderlab_exchange_table() {

	$plugin = new ViderLab_Exchange_Table();
	$plugin->run();

}
run_viderlab_exchange_table();
