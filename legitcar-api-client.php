<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://legitcar.ng
 * @since             1.0.0
 * @package           LegitCar_API_Client
 *
 * @wordpress-plugin
 * Plugin Name:       LEGITCAR API Client
 * Plugin URI:        http://legitcar.ng/
 * Description:       Consume LegiCar (legitcar.ng) VIN verification API from WordPress.
 * Version:           1.0.0
 * Author:            Samuel Ogbujimma
 * Author URI:        http://twitter.com/samuelik3chukwu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       legitcar-api-client
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('LEGITCAR_API_CLIENT_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-legitcar-api-client-activator.php
 */
function activate_legitcar_api_client()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-legitcar-api-client-activator.php';
	LegitCar_API_Client_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-legitcar-api-client-deactivator.php
 */
function deactivate_legitcar_api_client()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-legitcar-api-client-deactivator.php';
	LegitCar_API_Client_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_legitcar_api_client');
register_deactivation_hook(__FILE__, 'deactivate_legitcar_api_client');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-legitcar-api-client.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_legitcar_api_client()
{
	$plugin = new LegitCar_API_Client();
}
run_legitcar_api_client();
