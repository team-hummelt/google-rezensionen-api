<?php

namespace Hupa\RezensionenApi;

use Exception;
use Google_Rezensionen_Api;
use stdClass;

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
 * Define the Google API database.
 *
 * Loads and defines the database for this plugin
 * so that it is ready for Rezensionen.
 *
 * @since      1.0.0
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */
class Google_Rezensionen_Api_Database {

	/**
	 * TRAIT of Default Settings.
	 *
	 * @since    1.0.0
	 */
	use Google_Rezensionen_Api_Defaults_Trait;

	/**
	 * The current version of the DB-Version.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $db_version The current version of the database Version.
	 */
	protected string $db_version;

	/**
	 * The current settings ID of the DB-Version.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $settings_id The current settings ID for the database Settings.
	 */
	protected string $settings_id;

	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var Google_Rezensionen_Api $main The main class.
	 */
	private Google_Rezensionen_Api $main;

	/**
	 * @param $dbVersion
	 * @param $settingsId
	 * @param Google_Rezensionen_Api $main
	 */
	public function __construct( $dbVersion, $settingsId, Google_Rezensionen_Api $main ) {
		$this->db_version  = $dbVersion;
		$this->main        = $main;
		$this->settings_id = $settingsId;
	}

	/**
	 * Insert | Update Table Editor
	 * INIT Function
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function update_create_google_rezensionen_api_database() {

		if(!is_dir(GOOGLE_REZENSIONEN_API_UPLOAD_DIR)){
			if(!mkdir(GOOGLE_REZENSIONEN_API_UPLOAD_DIR, 0755, true) ){
				throw new Exception(__('Upload directory could not be created.', 'google-rezensionen-api'));
			}
		}
		if ( $this->db_version !== get_option( 'jal_google_rezensionen_api_db_version' ) ) {
			$this->create_google_rezensionen_api_database();
			$this->set_google_rezensionen_api_defaults();
			update_option( 'jal_google_rezensionen_api_db_version', $this->db_version );
		}

		$dbData = $this->get_google_api_rezension();
		if ( $dbData->status ) {
			$this->update_all_rezensionen($dbData->record);
		}
	}

	public function create_google_rezensionen_api_database() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;
		$table_name      = $wpdb->prefix . $this->table_google_api_settings;
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        app_settings text NULL,
        api_sync_settings text NULL,
        user_capability varchar(125) NOT NULL DEFAULT 'manage_options',
       PRIMARY KEY (id)
     ) $charset_collate;";
		dbDelta( $sql );


		$table_name      = $wpdb->prefix . $this->table_google_api_rezensionen;
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
    	`place_id` varchar(125) NOT NULL, 
        `aktiv` tinyint(1) NOT NULL DEFAULT 1,
        `show_star_level` tinyint(2) NOT NULL DEFAULT 0,
        `template_select` tinyint(2) NOT NULL DEFAULT 1,
        `formatted_address` varchar(255) NOT NULL,
        `address_components` text,
        `types` text,
        `reviews` longtext NOT NULL DEFAULT '',
        `name`  varchar(125) NOT NULL, 
        `website` varchar(255) NULL,
        `map_url` varchar(165) NOT NULL, 
        `map_image` varchar(255) NULL, 
        `map_settings` text,
        `user_rating` varchar(6) NOT NULL DEFAULT '0',
        `user_ratings_total` int(6) NOT NULL DEFAULT 0,
        `formatted_phone_number` varchar(255) NULL,
        `international_phone_number` varchar(255) NULL,
        `automatic_aktiv` tinyint(1) NOT NULL DEFAULT 1,
        `synchronization_intervall` tinyint(2) NOT NULL DEFAULT 2,
        `next_synchronization` varchar(28) NULL,
        `last_update` varchar(28) NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       PRIMARY KEY (place_id)
     ) $charset_collate;";
		dbDelta( $sql );

		$table_name      = $wpdb->prefix . $this->table_countries;
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
 		`code` char(2) CHARACTER SET utf8 NOT NULL,
 		`en` varchar(100)  NOT NULL DEFAULT '',
 		`de` varchar(100)  NOT NULL DEFAULT '',
 		`es` varchar(100)  NOT NULL,
 		`fr` varchar(100)  NOT NULL,
 		`it` varchar(100)  NOT NULL,
 		`ru` varchar(100)  NOT NULL,
         PRIMARY KEY (`code`),
 		 KEY `de` (`de`),
  		 KEY `en` (`en`)
     ) $charset_collate;";
		dbDelta( $sql );
	}

	public function set_google_rezensionen_api_defaults() {

		$settings = $this->getHupaGoogleRezensionenApiSettings( 'app_settings' );
		if ( ! $settings->status ) {
			$defaults    = $this->get_theme_default_settings();
			$insert = [
				'app_settings'      => json_encode( $defaults['default_settings']['app_settings'] ),
				'api_sync_settings' => json_encode( $defaults['api_sync_settings'] )
			];

			$this->setHupaGoogleRezensionenDefaultSettings($insert);
		}


		$countries = $this->get_google_api_countries_by_args();
		if ( ! $countries->status ) {
			$this->set_default_google_api_rezensionen_countries();
		}

	}

	/**
	 * Get Plugin Settings by Settingsname
	 *
	 * @param string $select
	 *
	 * @return object
	 */
	public function getHupaGoogleRezensionenApiSettings( string $select = '' ): object {
		global $wpdb;
		$return         = new stdClass();
		$return->status = false;
		$select ? $sel = $select : $sel = '*';
		$table          = $wpdb->prefix . $this->table_google_api_settings;
		$where          = sprintf( 'WHERE id=%d', $this->settings_id );
		$result         = $wpdb->get_row( "SELECT {$sel} FROM {$table} {$where}" );

		if ( ! $result ) {
			return $return;
		}
		$return->status  = true;
		if($select){
			$return->$select = $result->$select;
			return $return;
		}

		$return->app_settings = json_decode( $result->app_settings );
		$return->api_sync_settings = json_decode( $result->api_sync_settings );
		$return->user_capability = $result->user_capability;
		return $return;
	}

	/**
	 * @param string $args
	 * @param bool $fetchType
	 * @param string $select
	 *
	 * @return object
	 */
	public function get_google_api_countries_by_args( string $args = '', bool $fetchType = true, string $select = '' ): object {
		$return         = new stdClass();
		$return->status = false;
		global $wpdb;
		$fetchType ? $fetch = 'get_results' : $fetch = 'get_row';
		$select ? $sel = $select : $sel = '*';
		$table  = $wpdb->prefix . $this->table_countries;
		$result = $wpdb->$fetch( "SELECT {$sel} FROM {$table} {$args}" );
		if ( ! $result ) {
			return $return;
		}

		$return->status = true;
		$return->record = $result;

		return $return;
	}

	private function set_default_google_api_rezensionen_countries() {
		$file      = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'countries.csv';
		$countries = $this->google_api_csv_to_array( $file, ';' );
		if (!is_array( $countries )) {
			exit();
		}

		foreach ( $countries as $tmp ) {
			$this->set_google_api_countries( $tmp );
		}
	}

	private function google_api_csv_to_array( $filename = '', $delimiter = ',' ): array {
		if ( ! file_exists( $filename ) || ! is_readable( $filename ) ) {
			exit();
		}
		$header = null;
		$data   = array();
		if ( ( $handle = fopen( $filename, 'r' ) ) !== false ) {
			while ( ( $row = fgetcsv( $handle, 1000, $delimiter ) ) !== false ) {
				if ( ! $header ) {
					$header = $row;
				} else {
					$data[] = array_combine( $header, $row );
				}
			}
			fclose( $handle );
		}

		return $data;
	}

	/**
	 * @param array $data
	 */
	public function set_google_api_countries( array $data ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table_countries;
		$wpdb->insert(
			$table,
			array(
				'code' => $data['code'],
				'en'   => $data['en'],
				'de'   => $data['de'],
				'es'   => $data['es'],
				'fr'   => $data['fr'],
				'it'   => $data['it'],
				'ru'   => $data['ru'],
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * @param $record
	 *
	 * @return object
	 */
	public function set_google_api_rezension( $record ):stdClass {

		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;

		$wpdb->insert(
			$table,
			array(
				'place_id'   => $record->place_id,
				'aktiv' => $record->aktiv,
				'user_ratings_total'   => $record->user_ratings_total,
				'user_rating'   => $record->user_rating,
                'reviews' => $record->reviews,
				'formatted_address'  => $record->formatted_address,
				'address_components' => $record->address_components,
				'types' => $record->types,
				'map_settings' => $record->map_settings,
				'name'   => $record->name,
				'website'   => $record->website,
				'map_url'   => $record->map_url,
				'formatted_phone_number'   => $record->formatted_phone_number,
				'international_phone_number'   => $record->international_phone_number,
				'next_synchronization'   => $record->next_synchronization,
				'last_update' => $record->last_update,
			),
			array( '%s','%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
		);
		$return = new stdClass();
		if (!$wpdb->insert_id) {
			$return->status = false;
			$return->msg = __('Data could not be saved!', 'google-rezensionen-api');
			$return->id = false;

			return $return;
		}
		$return->status = true;
		$return->msg = __('Data saved!', 'google-rezensionen-api');
		$return->id = $wpdb->insert_id;

		return $return;
	}

	/**
	 * @param $record
	 *
	 */
	public function set_google_api_field( $record )
	{

		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;
		$wpdb->update(
			$table,
			array(
				$record->field => $record->value,
			),
			array('place_id' => $record->place_id),
			array( $record->type),
			array('%s')
		);
	}

	/**
	 * @param array $data
	 * @param false $update_now
	 * @param string $placeId
	 *
	 * @return string|void
	 */
	public function update_all_rezensionen(array $data, bool $update_now = false, string $placeId = '')
	{

		if(!is_array($data)){
			exit();
		}
		$record = new stdClass();
		global $google_api_handle;

		if($update_now){
			$args   = sprintf( 'WHERE place_id="%s"', $placeId );
		} else {
			$args = '';
		}
		$dbData = apply_filters( $this->main->get_plugin_name() . '/get_api_rezension', $args);
		if(!$dbData){
			exit();
		}

		$settings = $this->getHupaGoogleRezensionenApiSettings();
		$app_settings = $settings->app_settings;

		if(!$app_settings->google_api_key){
			exit();
		}

		foreach ($dbData->record as $tmp){
			if(!$update_now && current_time('timestamp') < strtotime($tmp->next_synchronization)){
				continue;
			}

			$googleJson = $google_api_handle->google_api_json_handle( $tmp->place_id, 'get_rezension_by_place_id' );
			if ( ! $googleJson->status ) {
				continue;
			}

			$result = $googleJson->record['result'];
			if ( isset( $result['types'] ) ) {
				$record->types = json_encode( $result['types'] );
			} else {
				$record->types = '';
			}

			$map_settings = json_decode($tmp->map_settings);
			$nextTime = '';
			if ( $tmp->automatic_aktiv == 1 ) {
				switch ( $tmp->synchronization_intervall ) {
					case '1':
						$nextTime = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) . '+1 day' ) );
						break;
					case'2':
						$nextTime = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) . '+1 week' ) );
						break;
					case'3':
						$nextTime = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) . '+1 month' ) );
						break;
				}
			}


			$record->place_id                   = $tmp->place_id;
			$record->formatted_address          = $result['formatted_address'];
            $record->reviews                    = $result['reviews'];
			$record->name                       = $result['name'];
			$record->website                    = $result['website'];
			$record->map_url                    = $result['url'];
			$record->user_rating                = $result['rating'];
			$record->user_ratings_total         = $result['user_ratings_total'];
			$record->formatted_phone_number     = $result['formatted_phone_number'];
			$record->international_phone_number = $result['international_phone_number'];
			$record->next_synchronization       = $nextTime;
			$record->last_update                = current_time( 'mysql' );
			$record->address_components         = json_encode( $result['address_components'] );

			$map_settings->map_scale_aktiv ? $scale = 'scale=2' : $scale = 'scale=1';

			$map_lat         = $result['geometry']['location']['lat'];
			$map_lng         = $result['geometry']['location']['lng'];

			$url = $app_settings->google_api_url . "staticmap?center={$map_lat},{$map_lng}&markers=color:red%7Clabel:%7C{$map_lat},{$map_lng}&zoom={$map_settings->map_zoom}&size={$map_settings->horizontal_size}x{$map_settings->vertical_size}&format={$map_settings->map_image_type}&{$scale}&maptype={$map_settings->map_type}&key={$app_settings->google_api_key}";
			$url = rawurldecode($url);
			$image = file_get_contents($url);
			$name = apply_filters($this->main->get_plugin_name().'/random_string',24, 0, 8) . '.' . $map_settings->map_image_type;
			$file = GOOGLE_REZENSIONEN_API_UPLOAD_DIR . $name;
			file_put_contents($file, $image);
			$record->map_image = $name;

			if(is_file(GOOGLE_REZENSIONEN_API_UPLOAD_DIR . $tmp->map_image)) {
				@unlink(GOOGLE_REZENSIONEN_API_UPLOAD_DIR . $tmp->map_image);
			}

			$newMapSettings = [
				'map_lat'         => $result['geometry']['location']['lat'],
				'map_lng'         => $result['geometry']['location']['lng'],
			];

			$dbMapSettings = json_decode($tmp->map_settings, true);
			$dbMapSettings = wp_parse_args( $newMapSettings,  $dbMapSettings );
			$record->map_settings = json_encode($dbMapSettings);
			$this->interval_update_rezension($record);
		}

		if($update_now){
			return $placeId;
		}

		return '';
	}

	/**
	 * @param $record
	 */
	private function interval_update_rezension($record) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;
		$wpdb->update(
			$table,
			array(
				'formatted_address'  => $record->formatted_address,
				'address_components' => $record->address_components,
				'types' => $record->types,
                'reviews' => $record->reviews,
				'name'   => $record->name,
				'website'   => $record->website,
				'map_url'   => $record->map_url,
				'map_image' => $record->map_image,
				'map_settings' => $record->map_settings,
				'user_rating'   => $record->user_rating,
				'user_ratings_total'   => $record->user_ratings_total,
				'formatted_phone_number'   => $record->formatted_phone_number,
				'international_phone_number'   => $record->international_phone_number,
				'next_synchronization'   => $record->next_synchronization,
				'last_update' => $record->last_update,
			),
			array('place_id' => $record->place_id),
			array( '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),
			array('%s')
		);
	}

	public function delete_google_api_rezension($place_id): void
	{
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;
		$wpdb->delete(
			$table,
			array(
				'place_id' => $place_id
			),
			array('%s')
		);
	}

	/**
	 * @param $record
	 */
	public function update_google_api_rezension($record): void
	{
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;
		$wpdb->update(
			$table,
			array(
				'place_id'   => $record->place_id,
				'aktiv' => $record->aktiv,
				'user_ratings_total'   => $record->user_ratings_total,
				'user_rating'   => $record->user_rating,
                'reviews' => $record->review,
				'formatted_address'  => $record->formatted_address,
				'address_components' => $record->address_components,
				'types' => $record->types,
				'map_settings' => $record->map_settings,
				'name'   => $record->name,
				'website'   => $record->website,
				'map_url'   => $record->map_url,
				'formatted_phone_number'   => $record->formatted_phone_number,
				'international_phone_number'   => $record->international_phone_number,
				'next_synchronization'   => $record->next_synchronization,
				'last_update' => $record->last_update,
			),
			array('place_id' => $record->place_id),
			array( '%s','%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),
			array('%s')
		);
	}

	/**
	 * @param $record
	 */
	public function update_google_api_static_image($record): void
	{
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;
		$wpdb->update(
			$table,
			array(
				'map_image' => $record->map_image,
			),
			array('place_id' => $record->place_id),
			array( '%s'),
			array('%s')
		);
	}

	/**
	 * @param $record
	 */
	public function update_rezension_optionen($record): void
	{
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_rezensionen;
		$wpdb->update(
			$table,
			array(
				'automatic_aktiv' => $record->automatic_aktiv,
				'synchronization_intervall' => $record->synchronization_intervall,
				'next_synchronization' => $record->next_synchronization,
				'last_update' => $record->last_update,
				'map_settings' => $record->map_settings,
			),
			array('place_id' => $record->place_id),
			array( '%s'),
			array('%s')
		);
	}

	/**
	 * @param string $args
	 * @param bool $fetchType
	 *
	 * @return object
	 */
	public function get_google_api_rezension( string $args = '', bool $fetchType = true): object {
		$return         = new stdClass();
		$return->status = false;
		global $wpdb;
		$fetchType ? $fetch = 'get_results' : $fetch = 'get_row';
		$table  = $wpdb->prefix . $this->table_google_api_rezensionen;
		$result = $wpdb->$fetch( "SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y %H:%i:%s') AS created,
       DATE_FORMAT(last_update, '%d.%m.%Y %H:%i:%s') AS lastUpdate, DATE_FORMAT(next_synchronization, '%d.%m.%Y %H:%i:%s') AS nextUpdate
			FROM {$table} {$args}" );
		if ( ! $result ) {
			return $return;
		}

		$return->status = true;
		$return->record = $result;

		return $return;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function setHupaGoogleRezensionenApiSettings( $key, $value ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_settings;
		$wpdb->insert(
			$table,
			array(
				'id' => $this->settings_id,
				$key => $value,
			),
			array( '%s' )
		);
	}

	/**
	 * @param array $record
	 */
	public function setHupaGoogleRezensionenDefaultSettings( array $record ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_settings;
		$wpdb->insert(
			$table,
			array(
				'app_settings' => $record['app_settings'],
				'api_sync_settings' => $record['api_sync_settings'],
			),
			array( '%s','%s' )
		);
	}

	/**
	 * @param string $code
	 *
	 * @return string
	 */
	public function google_api_check_countries( string $code ): string {
		$dbCountries = [ 'en', 'de', 'es', 'fr', 'it', 'ru' ];
		if ( in_array( $code, $dbCountries ) ) {
			return $code;
		}

		return 'de';
	}

	public function updateHupaGoogleRezensionenApiSettings( object $record ): void {
		foreach ( $record as $key => $val ) {
			$this->updateSettings( $key, $val );
		}
	}

	/**
	 * @param $key
	 * @param $value
	 */
	private function updateSettings( $key, $value ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table_google_api_settings;
		$wpdb->update(
			$table,
			array(
				$key => $value,
			),
			array( 'id' => $this->settings_id ),
			array( '%s' )
		);
	}
}