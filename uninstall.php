<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'api_rezensionen_settings';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

$table_name = $wpdb->prefix . 'api_rezensionen';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

$table_name = $wpdb->prefix . 'api_countries';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

$table_name = $wpdb->prefix . 'google_rezension_extensions';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

delete_option('jal_google_rezensionen_api_db_version');
delete_option('google-rezensionen-api-rest-extension-api-options');

$upload_dir = wp_get_upload_dir();
$delDir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'google-rezensionen-api-files' . DIRECTORY_SEPARATOR;
wwdhDestroyDir($delDir);

function wwdhDestroyDir($dir): bool
{
    if (!is_dir($dir) || is_link($dir))
        return unlink($dir);

    foreach (scandir($dir) as $file) {
        if ($file == "." || $file == "..")
            continue;
        if (!wwdhDestroyDir($dir . "/" . $file)) {
            chmod($dir . "/" . $file, 0777);
            if (!wwdhDestroyDir($dir . "/" . $file)) return false;
        }
    }
    return rmdir($dir);
}
