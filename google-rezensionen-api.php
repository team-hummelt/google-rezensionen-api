<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.hummelt-werbeagentur.de/
 * @since             1.0.0
 * @package           Google_Rezensionen_Api
 *
 * @wordpress-plugin
 * Plugin Name:       HUPA Google Rezensionen
 * Plugin URI:        https://www.hummelt-werbeagentur.de/
 * Description:       HUPA Google Reviews allows you to easily filter Google reviews and display them on your page.
 * Version:           1.0.2
 * Author:            Jens Wiecker
 * Author URI:        https://www.hummelt-werbeagentur.de/
 * License:           MIT License
 * Stable tag:        1.0.2
 * Tested up to:      5.9
 * Requires PHP:      7.4
 * Requires at least: 5.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * @since             1.0.0
 */
$plugin_data = get_file_data(dirname(__FILE__) . '/google-rezensionen-api.php', array('Version' => 'Version'), false);
define("GOOGLE_REZENSIONEN_API_VERSION", $plugin_data['Version']);
/**
 * Currently DATABASE VERSION
 * @since             1.0.0
 */
const GOOGLE_REZENSIONEN_API_DB_VERSION = '1.0.2';
const GOOGLE_REZENSIONEN_EXTENSION_API_DB_VERSION = '1.0.0';

/**
 * MIN PHP VERSION for Activate
 * @since             1.0.0
 */
const GOOGLE_REZENSIONEN_API_PHP_VERSION = '7.4';

/**
 * MIN WordPress VERSION for Activate
 * @since             1.0.0
 */
const GOOGLE_REZENSIONEN_API_WP_VERSION = '5.6';

/**
 * PLUGIN SLUG
 * @since             1.0.0
 */
define('GOOGLE_REZENSIONEN_API_SLUG_PATH', plugin_basename(__FILE__));

/**
 * PLUGIN BASENAME
 * @since             1.0.0
 */
define('GOOGLE_REZENSIONEN_API_BASENAME', plugin_basename(__DIR__));

/**
 * PLUGIN DIR
 * @since             1.0.0
 */
define('GOOGLE_REZENSIONEN_API_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR );

/**
 * PLUGIN ADMIN DIR
 * @since             1.0.0
 */
const GOOGLE_REZENSIONEN_API_ADMIN_DIR = GOOGLE_REZENSIONEN_API_DIR . 'admin' . DIRECTORY_SEPARATOR;

/**
 * PLUGIN Gutenberg Build DIR
 * @since             1.0.0
 */
const GOOGLE_REZENSION_API_GB_BUILD_DIR = GOOGLE_REZENSIONEN_API_ADMIN_DIR . 'Gutenberg' . DIRECTORY_SEPARATOR . 'plugin-data' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;


/**
 * PLUGIN EXTENSION API DIR
 * @since             1.0.0
 */
const GOOGLE_REZENSIONEN_API_CURL_DIR = GOOGLE_REZENSIONEN_API_DIR . 'api' . DIRECTORY_SEPARATOR;

/**
 * Default Settings ID
 * @since             1.0.0
 */
const GOOGLE_REZENSIONEN_API_SETTINGS_ID = 1;

/**
 * PLUGIN UPLOAD DIR for Formular Upload Function
 * @since             1.0.0
 */
$upload_dir = wp_get_upload_dir();
define("GOOGLE_REZENSIONEN_API_UPLOAD_DIR", $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'google-rezensionen-api-files' . DIRECTORY_SEPARATOR);
define("GOOGLE_REZENSIONEN_API_UPLOAD_URL", $upload_dir['baseurl'] . '/google-rezensionen-api-files/');


/**
 * Extension Update Check Time
 * @since             1.0.0
 */
const GOOGLE_REZENSION_UPDATE_EXTENSION_TIME = 43200;
//const GOOGLE_REZENSION_UPDATE_EXTENSION_TIME = 10;
/**
 * PLUGIN API DIR
 * @since             1.0.0
 */
define('GOOGLE_REZENSION_EXTENSION_API_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR);

/**
 * PLUGIN EXTENSION API DIR
 * @since             1.0.0
 */
define('GOOGLE_REZENSION_EXTENSION_DIR',  plugin_dir_path(__FILE__) . 'extensions' . DIRECTORY_SEPARATOR);


/**
 * PLUGIN PREVIEW EXTENSION PREVIEW DIR
 * @since             1.0.0
 */
const GOOGLE_REZENSION_EXTENSION_PREVIEW_DIR = GOOGLE_REZENSION_EXTENSION_DIR . 'preview' . DIRECTORY_SEPARATOR;


/**
 * PLUGIN PREVIEW EXTENSION INSTALLED DIR
 * @since             1.0.0
 */
const GOOGLE_REZENSION_EXTENSION_INSTALL_DIR = GOOGLE_REZENSION_EXTENSION_DIR . 'installed' . DIRECTORY_SEPARATOR;


/**
 * PLUGIN EXTENSION PREVIEW URL
 * @since             1.0.0
 */
define('GOGGLE_REZENSION_EXTENSION_PREVIEW_URL',  plugins_url(GOOGLE_REZENSIONEN_API_BASENAME) . '/extensions/preview/' );

/**
 * PLUGIN ID_RSA DIR
 * @since             1.0.0
 */
const GOOGLE_REZENSION_ID_RSA_DIR = GOOGLE_REZENSION_EXTENSION_API_DIR . 'id_rsa' . DIRECTORY_SEPARATOR;

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-google-rezensionen-api-activator.php
 */
function activate_google_rezensionen_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class_google_rezensionen_api_activator.php';
	Google_Rezensionen_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-google-rezensionen-api-deactivator.php
 */
function deactivate_google_rezensionen_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class_google_rezensionen_api_deactivator.php';
	Google_Rezensionen_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_google_rezensionen_api' );
register_deactivation_hook( __FILE__, 'deactivate_google_rezensionen_api' );


require_once 'extensions/class-google-rezension-extensions-installed.php';
$googleRezensionExtensionsInstalled = Goggle_Rezension_Extensions_Installed::instance();
$googleRezensionExtensionsInstalled->google_rezension_installed_extensions();
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class_google_rezensionen_api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
global $hupa_google_rezensionen;
$hupa_google_rezensionen = new Google_Rezensionen_Api();
$hupa_google_rezensionen->run();
