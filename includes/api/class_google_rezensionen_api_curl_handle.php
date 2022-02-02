<?php

namespace Rezensionen\ApiCurlHandle;

/**
 * Define the Google API functionality.
 *
 * Loads and defines the API files for this plugin
 * so that it is ready for Rezensionen.
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 */

/**
 * Define the Google API functionality.
 *
 * Loads and defines the API files for this plugin
 * so that it is ready for Rezensionen.
 *
 * @since      1.0.0
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */

use Google_Rezensionen_Api;
use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;


defined('ABSPATH') or die();

class Google_Rezensionen_Api_Curl_Handle {

	/**
	 * TRAIT of Default Settings.
	 *
	 * @since    1.0.0
	 */
	use Google_Rezensionen_Api_Defaults_Trait;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $basename    The ID of this plugin.
	 */
	private string $basename;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private string $version;

	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var Google_Rezensionen_Api $main The main class.
	 */
	private Google_Rezensionen_Api $main;


	/**
	 * @param string $plugin_name
	 * @param string $version
	 * @param Google_Rezensionen_Api $main
	 */
	public function __construct( string $plugin_name, string $version,Google_Rezensionen_Api  $main ) {

		$this->basename = $plugin_name;
		$this->version = $version;
		$this->main = $main;

	}

	/**
	 * @param $string
	 * @param $method
	 * @param string $countries
	 *
	 * @return object
	 */
	public function google_api_json_handle( $string, $method, string $countries = ''): object {

		global $google_api_database;
		$response         = new stdClass();
		$response->status = false;
		$response->count  = 0;
		$settings         = $google_api_database->getHupaGoogleRezensionenApiSettings( 'app_settings' );
		$settings         = json_decode( $settings->app_settings );

		switch ($method){
			case 'place_id':
				$url              = $settings->google_api_url . "place/findplacefromtext/json?input=%s&inputtype=textquery&fields=place_id,name,formatted_address&key=" . $settings->google_api_key;
				$record           = $this->curl_google_api_response( $url, $string );

				if(!$record->status || !$record->http_status == '200'){
					return $response;
				}
				$data = $record->record;
				if(!$data['status'] == 'ok') {
					return $response;
				}
				$response->status = true;
				$response->count = count($data['candidates']);
				$response->record = $data['candidates'];

				break;
			case'search_by_place_id':
				$url = $settings->google_api_url . "place/details/json?placeid=$string&fields=place_id,name,formatted_address&key=" . $settings->google_api_key;
				$record = $this->curl_google_api_response( $url );

				if(!$record->status || !$record->http_status == '200'){
					return $response;
				}

				$data = $record->record;
				if(!$data['status'] == 'ok') {
					return $response;
				}

				$response->status = true;
				$response->count = '1';
				$response->record = array($data['result']);
				return $response;

			case'auto_completion':
				if(!$countries) {
					return $response;
				}
				$url = $settings->google_api_url . "place/textsearch/json?query=%s&region=$countries&key=". $settings->google_api_key;
				$record = $this->curl_google_api_response( $url, $string );
				$data = $record->record;
				if(!$data['status'] == 'ok') {
					return $response;
				}
				$response->status = true;
				$response->count = count($data['results']);
				$response->record = $data['results'];
				break;
			case'get_rezension_by_place_id':
				$url = $settings->google_api_url . "place/details/json?placeid=$string&fields=name,url,user_ratings_total,rating,formatted_address,types,address_components,geometry/location,formatted_phone_number,international_phone_number,website&key=" . $settings->google_api_key;
				$record = $this->curl_google_api_response( $url );

				if(!$record->status || !$record->http_status == '200'){
					return $response;
				}

				$data = $record->record;
				if(!$data['status'] == 'ok') {
					return $response;
				}

				$response = $record;
				break;
		}

		return $response;
	}

	public function get_google_api_static_map($record): string {
		global $google_api_database;
		$response         = new stdClass();
		$response->status = false;
		$response->count  = 0;
		$settings = $google_api_database->getHupaGoogleRezensionenApiSettings();
		$app_settings = $settings->app_settings;
		$static_settings = $settings->api_sync_settings;
		$static_settings->scale2_aktiv ? $scale = 'scale=2' : $scale = 'scale=1';
		$url = $app_settings->google_api_url . "staticmap?center={$record->map_lat},{$record->map_lng}&markers=color:red%7Clabel:%7C{$record->map_lat},{$record->map_lng}&zoom={$record->map_zoom}&size={$static_settings->horizontal_size}x{$static_settings->vertical_size}&format={$static_settings->map_image_format}&{$scale}&maptype={$record->map_type}&key={$app_settings->google_api_key}";
		$url = rawurldecode($url);
		$image = file_get_contents($url);
		$name = apply_filters($this->basename.'/random_string',24, 0, 8) . '.' . $static_settings->map_image_format;
		$file = GOOGLE_REZENSIONEN_API_UPLOAD_DIR . $name;
		file_put_contents($file, $image);
		return $name;
	}

	/**
	 * @param string $api_url
	 * @param string $replace
	 *
	 * @return object
	 */
	private function curl_google_api_response( string $api_url, string $replace = '' ): object {

		$ch     = curl_init();
		$url = $api_url;
		if($replace){
			$replace = curl_escape( $ch, $replace );
			$url    = sprintf( $api_url, $replace );
		}

		curl_setopt_array( $ch, array(
				CURLOPT_URL            => $url,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER     => array()
			)
		);
		$response            = curl_exec( $ch );
		$return              = new stdClass();
		$return->status      = false;
		$return->http_status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$return->status      = false;
		if ( curl_errno( $ch ) ) {
			$return->err_msg = 'Curl-Fehler: ' . curl_error( $ch );
			curl_close( $ch );

			return $return;
		} else {
			curl_close( $ch );
			if ( $response ) {
				$return->status = true;
				$return->record = json_decode( $response, true );
			}
		}
		return $return;
	}
}