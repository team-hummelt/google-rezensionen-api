<?php
namespace Rezensionen\SrvApi\Endpoint;


use Google_Rezensionen_Api;
use Google_Rezensionen_Rest_Extension;
use stdClass;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * SRV-API ENDPOINT
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 */


defined('ABSPATH') or die();

class Srv_Api_Enpoint {

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

    public function __construct($plugin_name,$plugin_version,  Google_Rezensionen_Api $main ) {
        $this->main = $main;
        $this->basename = $plugin_name;
        $this->version = $plugin_version;

    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {

        $version = $this->version;
        $namespace = 'plugin/'.$this->basename.'/v' . $version;
        $base = '/';

        @register_rest_route(
            $namespace,
            $base,
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_registered_items'),
                'permission_callback' => array($this, 'permissions_check')
            )
        );

        @register_rest_route(
            $namespace,
            $base . '(?P<method>[\S^/]+)',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'plugin_google_rezensionen_api_rest_post_endpoint'),
                'permission_callback' => array($this, 'permissions_check')
            )
        );
    }


    /**
     * Get a collection of items.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_registered_items(WP_REST_Request $request)
    {
        $data = [];

        return rest_ensure_response($data);

    }

    /**
     * Get one item from the collection.
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function plugin_google_rezensionen_api_rest_post_endpoint(WP_REST_Request $request) {

        $method = $request->get_param('method');
        $data = $this->prepare_item_for_response($method);
        return rest_ensure_response($data);
    }


    public function prepare_item_for_response($method):array{

        $response = [];
        switch ($method){
            case'test':
                    return [
                        'test' => 'meine Daten',
                        'post'=>$_POST
                    ];
        }

        return $response;
    }

    /**
     * Check if a given request has access.
     *
     * @return string
     */
    public function permissions_check(): string
    {
        return '__return_true';
    }
}
