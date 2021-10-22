<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              victorcrespo.net
 * @since             0.4.1
 * @package           Codigo_postal
 *
 * @wordpress-plugin
 * Plugin Name:       Codigo Postal
 * Plugin URI:        codigopostal.com
 * Description:       Custom plugin for codigopostal.com
 * Version:           0.4.1
 * Author:            Victor Crespo
 * Author URI:        victorcrespo.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       codigo_postal
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.4.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CODIGO_POSTAL_VERSION', '0.4.1' );

/**
 * plugin path
 */
define( 'CP_PLUGIN_PATH', plugin_dir_path(__FILE__) );

wp_enqueue_script("jquery");

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-codigo_postal-activator.php
 */
function activate_codigo_postal() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-codigo_postal-activator.php';
	Codigo_postal_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-codigo_postal-deactivator.php
 */
function deactivate_codigo_postal() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-codigo_postal-deactivator.php';
	Codigo_postal_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_codigo_postal' );
register_deactivation_hook( __FILE__, 'deactivate_codigo_postal' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-codigo_postal.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_codigo_postal() {

	$plugin = new Codigo_postal();
	$plugin->run();

}
add_action('admin_menu', 'geolocation_add_custom_box');
function geolocation_add_custom_box() {
        if (function_exists('add_meta_box')) {
            add_meta_box('geolocation_sectionid', __('Geolocation', 'geolocation'), 'geolocation_inner_custom_box', 'post', 'advanced');
        } else {
            add_action('dbx_post_advanced', 'geolocation_old_custom_box');
        }
}

function geolocation_inner_custom_box() {
		echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="'.
		wp_create_nonce(plugin_basename(__FILE__)).'" />';
		echo '
		<label class="screen-reader-text" for="geolocation-address">Geolocation</label>
		<div class="taghint">'.__('Enter your address', 'geolocation').'</div>
		<input type="text" id="geolocation-address" name="geolocation-address" class="newtag form-input-tip" size="50" autocomplete="off" value="" />
		<input id="geolocation-load" type="button" class="button geolocationadd" value="'.__('Load', 'geolocation').'" tabindex="3" />
		<input type="hidden" id="geolocation-latitude" name="geolocation-latitude" />
		<input type="hidden" id="geolocation-longitude" name="geolocation-longitude" />
		<div id="geolocation-map" style="border:solid 1px #c6c6c6;width:500px;height:400px;margin-top:5px;"></div>
		<div style="margin:5px 0 0 0;">
			<input id="geolocation-public" name="geolocation-public" type="checkbox" value="1" />
			<label for="geolocation-public">'.__('Public', 'geolocation').'</label>
			<div style="float:right">
				<input id="geolocation-enabled" name="geolocation-on" type="radio" value="1" />
				<label for="geolocation-enabled">'.__('On', 'geolocation').'</label>
				<input id="geolocation-disabled" name="geolocation-on" type="radio" value="0" />
				<label for="geolocation-disabled">'.__('Off', 'geolocation').'</label>
			</div>
		</div>
	';
}
run_codigo_postal();
