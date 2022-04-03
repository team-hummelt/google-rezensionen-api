<?php

namespace Goggle\Rezension;
defined('ABSPATH') or die();

/**
 * The ADMIN Default Settings Trait.
 *
 * @since      1.0.0
 * @package    Experience_Reports
 * @subpackage Experience_Reports/extensions
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
trait Trait_Extension_Defaults
{
    //DATABASE TABLES
    protected string $table_wwdh_extensions = 'google_rezension_extensions';
    //SETTINGS DEFAULT OBJECT
    protected array $extension_default_values;
    //API Options
    protected string $api_url = 'https://start.hu-ku.com/theme-update/api/v2/';
    protected string $public_api_token_uri = 'public/token';
    protected string $public_api_support_uri = 'public';
    protected string $public_api_public_resource_uri = 'public/resource';
    protected string $public_api_public_preview_uri = 'public/preview';
    protected string $kunden_login_url = 'https://start.hu-ku.com/theme-update/kunden-web';

    // Get activate Token
    protected string $extension_api_activate_uri = 'jwt/extension/license/activate/';
    //Resource Token
    protected string $extension_api_id_rsa_token = 'jwt/extension/license/token/';
    // License Resource URI
    protected string $extension_api_resource_uri = 'jwt/extension/license/resource';
    protected string $extension_api_extension_download = 'jwt/extension/download';

    /**
     * @param string $args
     * @return array
     */
    protected function get_theme_default_settings(string $args = ''): array
    {
        $this->extension_default_values = [
            'api_settings' => [
                'api_url' => $this->api_url,
                'public_api_token_url' => $this->api_url . $this->public_api_token_uri,
                'public_api_support_url' => $this->api_url . $this->public_api_support_uri,
                'public_api_resource_url' => $this->api_url . $this->public_api_public_resource_uri,
                'public_api_preview_url' => $this->api_url . $this->public_api_public_preview_uri,
                //Kunden Login
                'kunden_login_url' => $this->kunden_login_url,
                'extension_api_activate_url' => $this->api_url . $this->extension_api_activate_uri,
                // ID_RSA Resource Token
                'extension_api_id_rsa_token' => $this->api_url . $this->extension_api_id_rsa_token,
                //Resource
                'extension_api_resource_url' => $this->api_url . $this->extension_api_resource_uri,
                //Download
                'extension_api_extension_download' => $this->api_url . $this->extension_api_extension_download,
            ],
            'extension_preview_language' => [
                'extension' => __('Extension', 'google-rezensionen-api'),
                'plugin_for' => __('Plugin for', 'google-rezensionen-api'),
                'status' => __('Status', 'google-rezensionen-api'),
                'details' => __('details', 'google-rezensionen-api'),
                'download' => __('download', 'google-rezensionen-api'),
                'activate' => __('activate', 'google-rezensionen-api'),
                'Activates' => __('Activates', 'google-rezensionen-api'),
                'activates' => __('activates', 'google-rezensionen-api'),
                'deactivated' => __('deactivated', 'google-rezensionen-api'),
                'licence' => __('license', 'google-rezensionen-api'),
                'license_details' => __('Licence details', 'google-rezensionen-api'),
                'back_btn' => __('back to the overview', 'google-rezensionen-api'),
                'Licence_for' => __('Licence for', 'google-rezensionen-api'),
                'Licence' => __('Licence', 'google-rezensionen-api'),
                'activation_code' => __('Activation-code', 'google-rezensionen-api'),
                'copies' => __('copies', 'google-rezensionen-api'),
                'copy' => __('copy', 'google-rezensionen-api'),
                'time_limit' => __('Time limit', 'google-rezensionen-api'),
                'url_limit' => __('Url-Limit', 'google-rezensionen-api'),
                'licence_start' => __('Licence start', 'google-rezensionen-api'),
                'licence_end' => __('Licence end', 'google-rezensionen-api'),
                'version' => __('Version', 'google-rezensionen-api'),
                'extension_for' => __('Extension for', 'google-rezensionen-api'),
                'file_size' => __('File size', 'google-rezensionen-api'),
                'type' => __('Type', 'google-rezensionen-api'),
                'php_min' => __('PHP Min', 'google-rezensionen-api'),
                'wp_min' => __('WP Min', 'google-rezensionen-api'),
                'month' => __('Month', 'google-rezensionen-api'),
                'months' => __('Months', 'google-rezensionen-api'),
                'at' => __('at', 'google-rezensionen-api'),
                'clock' => __('Clock', 'google-rezensionen-api'),
                'unlimited' => __('unlimited', 'google-rezensionen-api'),
                'installations' => __('Installations', 'google-rezensionen-api'),
                'installation' => __('Installation', 'google-rezensionen-api'),
                'url_licence' => __('URL Licence', 'google-rezensionen-api'),
                'not_activated' => __('not activated', 'google-rezensionen-api'),
                'help_header' => __( 'Call rest endpoint', 'google-rezensionen-api' ),
                'help_js' => __( 'jQuery / Javascript examples to retrieve the data.', 'google-rezensionen-api' ),
            ],
        ];

        if ($args) {
            return $this->extension_default_values[$args];
        } else {
            return $this->extension_default_values;
        }
    }

}