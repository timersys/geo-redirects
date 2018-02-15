<?php

/**
 * @link              https://timersys.com
 * @since             1.0.0
 * @package           Geotr
 *
 * @wordpress-plugin
 * Plugin Name:       Geo Redirects
 * Plugin URI:        https://geotargetingwp/
 * Description:       Create redirects based on Countries, Cities or States. Add multiple rules
 * Version:           1.2.3
 * Author:            Damian Logghe
 * Author URI:        https://timersys.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geotr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GEOTR_VERSION', '1.2.3');
define( 'GEOTR_PLUGIN_FILE' , __FILE__);
define( 'GEOTR_DIR', dirname(__FILE__));
define( 'GEOTR_URL', plugin_dir_url(__FILE__));
define( 'GEOTR_PLUGIN_HOOK' , basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
if( !defined('GEOTROOT_PLUGIN_FILE'))
	define( 'GEOTROOT_PLUGIN_FILE', GEOTR_PLUGIN_FILE );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-geotr-activator.php
 */
function activate_geotr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geotr-activator.php';
	Geotr_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-geotr-deactivator.php
 */
function deactivate_geotr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geotr-deactivator.php';
	Geotr_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_geotr' );
register_deactivation_hook( __FILE__, 'deactivate_geotr' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-geotr.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_geotr() {

	return Geotr::instance();

}
$GLOBALS['geotr'] = run_geotr();
