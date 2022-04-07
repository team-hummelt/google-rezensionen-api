<?php
namespace Rezensionen\SrvApi\Endpoint;


use Google_Rezensionen_Api;
use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;

/**
 * API EXEC WP-Remote
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 */

defined('ABSPATH') or die();

if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'is_user_logged_in' ) ) {
    require_once ABSPATH . 'wp-includes/pluggable.php';
}

class Make_Remote_Exec
{
    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @access   private
     * @var Google_Rezensionen_Api $main The main class.
     */
    private Google_Rezensionen_Api $main;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $basename The ID of this plugin.
     */
    private string $basename;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private string $version;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      object $config The current version of this plugin.
     */
    private object $config;

    /**
     * TRAIT of Default Settings.
     *
     * @since    1.0.0
     */
    use Google_Rezensionen_Api_Defaults_Trait;

    private static $instance;


    /**
     * @return static
     */
    public static function instance(string $basename, string $version, Google_Rezensionen_Api $main): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($basename, $version, $main);
        }
        return self::$instance;
    }

    public function __construct(string $basename, string $version, Google_Rezensionen_Api $main)
    {
        $this->basename = $basename;
        $this->version = $version;
        $this->main = $main;

        if (!get_option("{$this->basename}_update_config")) {
            $config = $this->main->get_plugin_api_config();
            $config->rest_url = rest_url('plugin/' . $this->basename . '/v' . $this->version . '/');
            $config->site_url = site_url();
            $config->license = 0;
            $config->basename = $this->basename;
            update_option("{$this->basename}_update_config", $config);
        }

        $this->config = get_option("{$this->basename}_update_config");
        //$this->check_is_plugin_aktiv();
    }

    public function make_api_exec_job($method, $body)
    {

       switch ($method){
           case 'update-config':
                if(!is_array($body)){
                  return [
                      'status' => 400,
                      'msg' => 'No data received',
                      'code' => 'no_data_received'
                  ];
                }

                if(!isset($body['body']['method']) || !isset($body['body']['access_token']) || !isset($body['body']['url'])){
                    return [
                        'status' => 400,
                        'msg' => 'No data received',
                        'code' => 'no_data_received'
                    ];
                }

                $args = [
                    'method' => $body['body']['method'],
                    'slug' => $this->basename,
                    'url' => site_url()
                ];

               $response = wp_remote_post($body['body']['url'], $this->config_post_args($body['body']['access_token'], $args));
               if (is_wp_error($response)) {
                   do_action($this->basename.'/set_api_log','error', $response->get_error_message());
                   return [
                       'status' => 400,
                       'msg' => 'No data received',
                       'code' => 'no_data_received'
                   ];
               }

               if(is_array($response) && isset($response['body']) && !empty($response['body'])){
                  $query = json_decode($response['body']);
                  if(isset($query->config)){

                     update_option("{$this->basename}_update_config", $query->config);
                      return [
                          'status' => 200,
                          'msg' => 'Config gespeichert: <b>Slug:</b> ' . $this->basename . ' | <b>URL:</b> '.site_url()
                      ];
                  }
               }

               return [
                   'status' => 400,
                   'msg' => 'No data received',
                   'code' => 'no_data_received'
               ];
       }
    }



    private function config_post_args($bearerToken, $body = []): array
    {
        return [
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'sslverify' => true,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => "Bearer $bearerToken"
            ],
            'body' => $body
        ];
    }

    private function check_is_plugin_aktiv(){
        if(!get_option("{$this->basename}_update_config")->aktiv){
            deactivate_plugins( GOOGLE_REZENSIONEN_API_SLUG_PATH );
        }
    }

}