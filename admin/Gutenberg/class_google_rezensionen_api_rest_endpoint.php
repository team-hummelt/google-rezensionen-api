<?php

namespace Rezensionen\Endpoints;
defined( 'ABSPATH' ) or die();

use Exception;
use Google_Rezensionen_Api;

use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * The BS-Formular2 Helper Class.
 *
 * @since      1.0.0
 * @package    Bs_Formular2
 * @subpackage Bs_Formular2/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Google_Rezensionen_Api_Rest_Endpoint {

	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @var Google_Rezensionen_Api $main The main class.
	 */
	protected Google_Rezensionen_Api $main;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $basename The ID of this plugin.
	 */
	private string $basename;

	/**
	 * TRAIT of Default Settings.
	 * @since    1.0.0
	 */
	use Google_Rezensionen_Api_Defaults_Trait;


	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private string $version;

	/**
	 * @param string $basename
	 * @param string $version
	 * @param Google_Rezensionen_Api $main
	 */
	public function __construct( string $basename, string $version, Google_Rezensionen_Api $main ) {
		$this->basename = $basename;
		$this->main     = $main;
		$this->version  = $version;
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'google-rezensionen-endpoint/v' . $version;
		$base      = '/';

		@register_rest_route(
			$namespace,
			$base . 'method/(?P<method>[\S]+)',
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'rezension_post_rest_endpoint_get_response'),
				'permission_callback' => array($this, 'permissions_check')
			)
		);
	}

	/**
	 * Get a collection of items.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return void
	 */
	public function get_items( WP_REST_Request $request ) {


	}

	/**
	 * Get one item from the collection.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function rezension_post_rest_endpoint_get_response( WP_REST_Request $request ) {

		$method = (string) $request->get_param( 'method' );

		if ( ! $method ) {
			return new WP_Error( 404, ' Method failed' );
		}

		return $this->get_method_item( $method );

	}

	/**
	 * GET Post Meta BY ID AND Field
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_method_item( $method ) {

		if ( ! $method ) {
			return new WP_Error( 404, ' Method failed' );
		}
		$response = new stdClass();
		switch ( $method ) {
			case 'get-rezension-data':
				$templates = apply_filters( $this->basename . '/google_api_selects', 'ausgabe_template_select' );

				$sendArr   = [];
				foreach ( $templates as $key => $val ) {
					$sendItems = [
						'id'   => $key,
						'name' => $val
					];
					$sendArr[] = $sendItems;
				}

				if(!$sendArr){
					return new WP_Error(404, 'No template data found');
				}

				$rezensionenData = apply_filters( $this->basename . '/get_api_rezension', '' );
				$rezensionArr = [];
				if($rezensionenData->status){
					foreach ($rezensionenData->record as $tmp){
						$rezItem = [
							'id'   => $tmp->place_id,
							'name' => $tmp->name
						];
						$rezensionArr[] = $rezItem;
					}
				}

				$response->rezensionen = $rezensionArr;
				$response->templates = $sendArr;

				return new WP_REST_Response($response, 200);
		}
	}

	/**
	 * Check if a given request has access.
	 *
	 * @return bool
	 */
	public function permissions_check(): bool {
		return current_user_can( 'edit_posts' );
	}
}
