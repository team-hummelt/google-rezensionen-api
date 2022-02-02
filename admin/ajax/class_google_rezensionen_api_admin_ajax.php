<?php

namespace Rezensionen\AdminAjax;
defined( 'ABSPATH' ) or die();


use Google_Rezensionen_Api;
use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Define the Google API AJAX functionality.
 *
 * Loads and defines the API Ajax files for this plugin
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
 * Loads and defines the API Ajax files for this plugin
 * so that it is ready for Rezensionen.
 *
 * @since      1.0.0
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */
class Google_Rezensionen_Api_Admin_Ajax {
	/**
	 * The AJAX METHOD
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $method The AJAX METHOD.
	 */
	protected string $method;

	/**
	 * The plugin Slug Path.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_dir plugin Slug Path.
	 */
	protected string $plugin_dir;

	/**
	 * TRAIT of Default Settings.
	 *
	 * @since    1.0.0
	 */
	use Google_Rezensionen_Api_Defaults_Trait;

	/**
	 * TWIG autoload for PHP-Template-Engine
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Environment $twig TWIG autoload for PHP-Template-Engine
	 */
	protected Environment $twig;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $basename The ID of this plugin.
	 */

	private string $basename;
	/**
	 * The Version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current Version of this plugin.
	 */
	private string $version;
	/**
	 * The AJAX DATA
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array|object $data The AJAX DATA.
	 */
	private  $data;
	/**
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var Google_Rezensionen_Api $main The main class.
	 */
	private Google_Rezensionen_Api $main;

	/**
	 * @param string $basename
	 * @param Google_Rezensionen_Api $main
	 * @param Environment $twig
	 */
	public function __construct( string $basename, Google_Rezensionen_Api $main, Environment $twig ) {
		$this->basename   = $basename;
		$this->plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->basename . DIRECTORY_SEPARATOR;
		$this->twig       = $twig;
		$this->method     = '';
		if ( isset( $_POST['daten'] ) ) {
			$this->data   = $_POST['daten'];
			$this->method = filter_var( $this->data['method'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		}

		if ( ! $this->method ) {
			$this->method = $_POST['method'];
		}

		$this->main = $main;
	}

	/**
	 * ADMIN AJAX RESPONSE.
	 * @return stdClass
	 * @since    1.0.0
	 */
	public function google_rezensionen_api_admin_ajax_handle(): stdClass {
		global $wpdb;
		$record               = new stdClass();
		$responseJson         = new stdClass();
		$responseJson->status = false;
		$responseJson->msg    = date( 'H:i:s', current_time( 'timestamp' ) );
		$ajax_msg             = $this->get_theme_default_settings( 'ajax_msg' );
		$errMsg               = $ajax_msg['error_msg'];
		global $google_api_handle;
		switch ( $this->method ) {
			case'google_api_options_handle':
				$responseJson->spinner = true;
				$api_key               = filter_input( INPUT_POST, 'api_key', FILTER_SANITIZE_STRING );
				$api_url               = filter_input( INPUT_POST, 'api_url', FILTER_VALIDATE_URL );
				$user_role             = filter_input( INPUT_POST, 'user_role', FILTER_SANITIZE_STRING );
				filter_input( INPUT_POST, 'ds_aktiv', FILTER_SANITIZE_STRING ) ? $dsAktiv = 1 : $dsAktiv = 0;
				filter_input( INPUT_POST, 'completion_aktiv', FILTER_SANITIZE_STRING ) ? $completion_aktiv = 1 : $completion_aktiv = 0;


				if ( ! $user_role ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				if ( ! $api_key ) {
					$responseJson->alert     = true;
					$responseJson->alert_msg = $this->get_theme_default_settings( 'ajax_msg', __( 'Google API Key', 'google-rezensionen-api' ) )['error_msg']['err_options_field'];
				}
				if ( ! $api_url ) {
					$responseJson->alert     = true;
					$responseJson->alert_msg = $this->get_theme_default_settings( 'ajax_msg', __( 'Google API URL', 'google-rezensionen-api' ) )['error_msg']['err_options_field'];
				}

				//TODO Deaktiviert
				$completion_aktiv = 0;
				$settings         = [
					'google_api_url'   => $api_url,
					'google_api_key'   => $api_key,
					'google_ds_show'   => $dsAktiv,
					'completion_aktiv' => $completion_aktiv
				];

				$record->user_capability = $user_role;
				$record->app_settings    = json_encode( $settings );
				apply_filters( $this->basename . '/update_settings', $record );
				$responseJson->status = true;
				break;

			case'set_api_sync_settings':
				$google_map_type  = filter_input( INPUT_POST, 'google_map_type', FILTER_SANITIZE_STRING );
				$horizontal_size  = filter_input( INPUT_POST, 'horizontal_size', FILTER_SANITIZE_NUMBER_INT );
				$vertical_size    = filter_input( INPUT_POST, 'vertical_size', FILTER_SANITIZE_NUMBER_INT );
				$static_card_zoom = filter_input( INPUT_POST, 'static_card_zoom', FILTER_SANITIZE_NUMBER_INT );
				$map_image_format = filter_input( INPUT_POST, 'map_image_format', FILTER_SANITIZE_STRING );
				filter_input( INPUT_POST, 'scale2_aktiv', FILTER_SANITIZE_STRING ) ? $scale2_aktiv = 1 : $scale2_aktiv = 0;

				$update_option   = filter_input( INPUT_POST, 'update_option', FILTER_SANITIZE_NUMBER_INT );
				$update_interval = filter_input( INPUT_POST, 'update_interval', FILTER_SANITIZE_NUMBER_INT );

				$update_option == 2 ? $interval = 2 : $interval = $update_interval;
				$api_sync_settings = [
					'google_map_type'  => esc_html( $google_map_type ) ?? 'hybrid',
					'horizontal_size'  => (int) $horizontal_size ?? 400,
					'vertical_size'    => (int) $vertical_size ?? 400,
					'scale2_aktiv'     => $scale2_aktiv,
					'map_image_format' => esc_html( $map_image_format ),
					'static_card_zoom' => (int) $static_card_zoom ?? 15,
					'update_interval'  => (int) $interval,
					'update_option'    => (int) $update_option
				];

				$record->api_sync_settings = json_encode( $api_sync_settings );
				apply_filters( $this->basename . '/update_settings', $record );
				$responseJson->spinner = true;
				$responseJson->status  = true;
				break;

			case'load_api_data':

				filter_input( INPUT_POST, 'policy_checked', FILTER_SANITIZE_STRING ) ? $policy_checked = 1 : $policy_checked = 0;
				filter_input( INPUT_POST, 'disable_policy', FILTER_SANITIZE_STRING ) ? $disable_policy = 1 : $disable_policy = 0;

				if ( ! $policy_checked ) {
					$responseJson->msg = $errMsg['err_ds_check'];

					return $responseJson;
				}

				$settings = apply_filters( $this->basename . '/get_settings', 'app_settings' );
				if ( $disable_policy ) {
					$settings                 = json_decode( $settings->app_settings );
					$settings->google_ds_show = 0;
					$record->app_settings     = json_encode( $settings );
					apply_filters( $this->basename . '/update_settings', $record );
				}

				$sendData               = json_decode( $settings->app_settings );
				$record->key            = base64_encode( $sendData->google_api_key );
				$record->callback       = 'initGmapsAutocomplete';
				$record->libraries      = 'places';
				$record->channel        = 2;
				$responseJson->url      = $sendData->google_api_url . 'js?';
				$responseJson->status   = true;
				$responseJson->record   = $record;
				$responseJson->callback = 'initGmapsAutocomplete';
				break;

			case'find_place_id':
			case'search_data_by_place_id':
				$search = filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING );

				if ( ! $search ) {
					$responseJson->msg         = $errMsg['search_empty'];
					$responseJson->input_false = true;

					return $responseJson;
				}

				$dbData = apply_filters( $this->basename . '/get_api_rezension', '' );
				if ( $dbData->status ) {
					$responseJson->msg        = $errMsg['version_error'];
					$responseJson->show_alert = true;

					return $responseJson;
				}
				if ( $this->method == 'search_data_by_place_id' ) {
					$googleJson = $google_api_handle->google_api_json_handle( $search, 'search_by_place_id' );

				} else {
					$googleJson = $google_api_handle->google_api_json_handle( $search, 'place_id' );
				}

				if ( ! $googleJson->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$responseJson->status = true;
				$responseJson->count  = $googleJson->count;

				if ( $googleJson->count == 1 ) {
					$lEntry = sprintf( __( '%s entry was found.', 'google-rezensionen-api' ), $googleJson->count );
				} else {
					$lEntry = sprintf( __( '%s entries were found.', 'google-rezensionen-api' ), $googleJson->count );
				}

				$googleJson->lang_google_found = $lEntry;
				$googleJson->lang              = $ajax_msg['templates'];

				try {
					$template               = $this->twig->render( 'place-id-search-result.twig', [ 'data' => $googleJson ] );
					$responseJson->template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
						'',
						' '
					), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
				} catch ( LoaderError | SyntaxError | RuntimeError $e ) {
					$responseJson->msg = $e->getMessage();
				} catch ( Throwable $e ) {
					$responseJson->msg = $e->getMessage();
				}
				$responseJson->status = true;

				break;

			case'search_auto_completion':
				$search  = filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING );
				$country = filter_input( INPUT_POST, 'country', FILTER_SANITIZE_STRING );
				if ( ! $search ) {
					return $responseJson;
				}

				$country = str_replace( [ ' ', '-' ], [ '', '#' ], $country );
				$country = explode( '#', $country );
				if ( ! isset( $country[0] ) ) {
					$responseJson->msg         = $errMsg['search_empty'];
					$responseJson->input_false = true;

					return $responseJson;
				}

				$googleJson = $google_api_handle->google_api_json_handle( $search, 'auto_completion', strtolower( $country[0] ) );

				if ( ! $googleJson->status ) {
					return $responseJson;
				}

				$retArr = [];
				foreach ( $googleJson->record as $tmp ) {
					if ( ! $tmp['place_id'] ) {
						continue;
					}
					$ret_item = [
						'place_id'     => $tmp['place_id'],
						'name_adresse' => $tmp['name'] . ' - ' . $tmp['formatted_address'],
					];
					$retArr[] = $ret_item;
				}
				if ( ! $retArr ) {
					return $responseJson;
				}

				$responseJson->status = true;
				$responseJson->record = $retArr;
				break;

			case 'set_new_rezension':
				$place_id = filter_input( INPUT_POST, 'place_id', FILTER_SANITIZE_STRING );
				if ( ! $place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$args   = sprintf( 'WHERE place_id="%s"', $place_id );
				$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
				if ( $dbData->status ) {
					$responseJson->msg = $errMsg['place_existing'];

					return $responseJson;
				}

				$googleJson = $google_api_handle->google_api_json_handle( $place_id, 'get_rezension_by_place_id' );

				if ( ! $googleJson->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$dbData   = apply_filters( $this->basename . '/get_settings', '' );
				$settings = $dbData->api_sync_settings;

				$result               = $googleJson->record['result'];
				$record->map_settings = json_encode( [
					'map_lat'         => $result['geometry']['location']['lat'],
					'map_lng'         => $result['geometry']['location']['lng'],
					'map_zoom'        => $settings->static_card_zoom,
					'map_type'        => $settings->google_map_type,
					'map_image_type'  => $settings->map_image_format,
					'map_scale_aktiv' => $settings->scale2_aktiv,
					'horizontal_size' => $settings->horizontal_size,
					'vertical_size'   => $settings->vertical_size,
				] );

				if ( isset( $result['types'] ) ) {
					$record->types = json_encode( $result['types'] );
				} else {
					$record->types = '';
				}

				$record->aktiv                      = 1;
				$record->place_id                   = $place_id;
				$record->formatted_address          = $result['formatted_address'];
				$record->name                       = $result['name'];
				$record->website                    = $result['website'];
				$record->map_url                    = $result['url'];
				$record->user_rating                = $result['rating'];
				$record->user_ratings_total         = $result['user_ratings_total'];
				$record->formatted_phone_number     = $result['formatted_phone_number'];
				$record->international_phone_number = $result['international_phone_number'];
				$record->next_synchronization       = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) . '+ 7day' ) );
				$record->last_update                = current_time( 'mysql' );
				$record->address_components         = json_encode( $result['address_components'] );
				apply_filters( $this->basename . '/set_api_rezension', $record );

				$record->map_lat         = $result['geometry']['location']['lat'];
				$record->map_lng         = $result['geometry']['location']['lng'];
				$record->map_zoom        = $settings->static_card_zoom;
				$record->map_type        = $settings->google_map_type;
				$record->map_image_type  = $settings->map_image_format;
				$record->map_scale_aktiv = $settings->scale2_aktiv;
				$staticImage             = $google_api_handle->get_google_api_static_map( $record );
				$updImg                  = [
					'place_id'  => $place_id,
					'map_image' => $staticImage,
				];


				apply_filters( $this->basename . '/update_static_image', (object) $updImg );

				$args   = sprintf( 'WHERE place_id="%s"', $place_id );
				$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
				if ( ! $dbData->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}
				$dbData = $dbData->record;

				$edit                   = $this->make_rezension_data( $dbData );
				$dbData->stars          = $edit->stars;
				$dbData->place_type     = $edit->place_type;
				$dbData->adresse        = $edit->adresse;
				$dbData->img_url        = $edit->img_url;
				$dbData->user_rating    = $edit->user_rating;
				$dbData->lang_rezension = $edit->lang_rezension;
				$dbData->google_url     = urlencode( $dbData->name );

				$dbData->l = $ajax_msg['templates'];
				try {
					$template               = $this->twig->render( 'one-rezension.twig', [ 'd' => $dbData ] );
					$responseJson->template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
						'',
						' '
					), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
				} catch ( LoaderError | SyntaxError | RuntimeError $e ) {
					$responseJson->msg = $e->getMessage();
				} catch ( Throwable $e ) {
					$responseJson->msg = $e->getMessage();
				}
				$responseJson->status = true;
				break;

			case'get_details_by_place_id':
				$place_id = filter_input( INPUT_POST, 'place_id', FILTER_SANITIZE_STRING );
				if ( ! $place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}
				break;


			case 'get_rezensionen_overview':
				$dbData       = apply_filters( $this->basename . '/get_api_rezension', '' );
				$settingsData = apply_filters( $this->basename . '/get_settings', '' );
				$settings     = $settingsData->app_settings;
				! $settings->google_api_key || ! $settings->google_api_url ? $showError = true : $showError = false;
				$record->l = $ajax_msg['templates'];
				if ( ! $settings->google_api_key || ! $settings->google_api_url || ! $dbData->status ) {
					$data = [
						'img_url' => plugins_url( $this->basename ) . '/admin/images/',
						'l'       => $record->l,
						'error'   => $showError
					];
					try {
						$template               = $this->twig->render( 'start-no-data-template.twig', [ 'data' => $data ] );
						$responseJson->template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
							'',
							' '
						), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
					} catch ( LoaderError | SyntaxError | RuntimeError $e ) {
						$responseJson->msg = $e->getMessage();
					} catch ( Throwable $e ) {
						$responseJson->msg = $e->getMessage();
					}

					return $responseJson;
				}

				$dataArr = [];
				foreach ( $dbData->record as $tmp ) {
					$edit                = $this->make_rezension_data( $tmp );
					$tmp->stars          = $edit->stars;
					$tmp->place_type     = $edit->place_type;
					$tmp->adresse        = $edit->adresse;
					$tmp->img_url        = $edit->img_url;
					$tmp->user_rating    = $edit->user_rating;
					$tmp->lang_rezension = $edit->lang_rezension;
					$tmp->google_url     = urlencode( $tmp->name );
					$dataArr[]           = $tmp;
				}


				$record->data = $dataArr;


				try {
					$template               = $this->twig->render( 'rezensionen-overview.twig', [ 'data' => $record ] );
					$responseJson->template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
						'',
						' '
					), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
				} catch ( LoaderError | SyntaxError | RuntimeError $e ) {
					$responseJson->msg = $e->getMessage();
				} catch ( Throwable $e ) {
					$responseJson->msg = $e->getMessage();
				}
				$responseJson->status = true;

				break;

			case'get_rezension_modal_data':
				$place_id             = filter_input( INPUT_POST, 'place_id', FILTER_SANITIZE_STRING );
				$responseJson->target = filter_input( INPUT_POST, 'target', FILTER_SANITIZE_STRING );
				if ( ! $place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$args   = sprintf( 'WHERE place_id="%s"', $place_id );
				$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
				if ( ! $dbData->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}
				$dbData                  = $dbData->record;
				$responseJson->id        = $dbData->place_id;
				$responseJson->name      = $dbData->name;
				$responseJson->aktiv     = $dbData->aktiv;
				$responseJson->status    = true;
				$responseJson->shortcode = '[google_rezension id="' . $dbData->place_id . '"]';

				break;

			case'update_rezension_aktiv':
				$record->place_id      = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
				$responseJson->spinner = true;
				if ( ! $record->place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				filter_input( INPUT_POST, 'aktiv', FILTER_SANITIZE_STRING ) ? $record->value = 1 : $record->value = 0;
				$record->field = 'aktiv';
				$record->type  = '%d';
				apply_filters( $this->basename . '/set_rezension_field', $record );
				$responseJson->status = true;
				break;

			case'delete_rezension':
				$place_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
				if ( ! $place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$args   = sprintf( 'WHERE place_id="%s"', $place_id );
				$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
				if ( ! $dbData->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}
				$dbData = $dbData->record;
				$img    = $dbData->map_image;
				if ( is_file( GOOGLE_REZENSIONEN_API_UPLOAD_DIR . $img ) ) {
					@unlink( GOOGLE_REZENSIONEN_API_UPLOAD_DIR . $img );
				}

				apply_filters( $this->basename . '/delete_rezension', $place_id );
				$responseJson->id     = $place_id;
				$responseJson->msg    = __( 'Review successfully deleted.', 'google-rezensionen-api' );

				$dbData                 = apply_filters( $this->basename . '/get_api_rezension', '' );
				$record->l              = $ajax_msg['templates'];
				$responseJson->template = false;
				if ( ! $dbData->status ) {
					$data = [
						'img_url' => plugins_url( $this->basename ) . '/admin/images/',
						'l'       => $record->l,
						'error'   => false
					];
					try {
						$template               = $this->twig->render( 'start-no-data-template.twig', [ 'data' => $data ] );
						$responseJson->template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
							'',
							' '
						), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
					} catch ( LoaderError | SyntaxError | RuntimeError $e ) {
						$responseJson->msg = $e->getMessage();
					} catch ( Throwable $e ) {
						$responseJson->msg = $e->getMessage();
					}
				}
				$responseJson->status = true;
				break;

			case'update_rezensionen_by_id':
				$place_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
				if ( ! $place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}
				$dbData = apply_filters( $this->basename . '/get_api_rezension', '' );
				if ( ! $dbData->status ) {
					$responseJson->msg = $errMsg['not_update'];

					return $responseJson;
				}
				apply_filters( $this->basename . '/update_all_rezensionen', $dbData->record, true, $place_id );
				$responseJson->status = true;
				$responseJson->id     = $place_id;
				$responseJson->msg    = __( 'Update performed successfully!', 'google-rezensionen-api' );
				break;

			case'get_settings_by_id_template':
				$place_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
				if ( ! $place_id ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$args   = sprintf( 'WHERE place_id="%s"', $place_id );
				$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
				if ( ! $dbData->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}
				$dbData       = $dbData->record;
				$map_settings = json_decode( $dbData->map_settings );

				$select                      = apply_filters( $this->basename . '/google_api_selects', 'update_time_select' );
				$template_select             = apply_filters( $this->basename . '/google_api_selects', 'ausgabe_template_select' );
				$map_typ_select              = apply_filters( $this->basename . '/google_api_selects', 'map_type_select' );
				$map_img_type_select         = apply_filters( $this->basename . '/google_api_selects', 'map_image_format' );
				$edit                        = $this->make_rezension_data( $dbData );
				$dbData->stars               = $edit->stars;
				$dbData->place_type          = $edit->place_type;
				$dbData->adresse             = $edit->adresse;
				$dbData->img_url             = $edit->img_url;
				$dbData->user_rating         = $edit->user_rating;
				$dbData->lang_rezension      = $edit->lang_rezension;
				$dbData->google_url          = urlencode( $dbData->name );
				$dbData->map                 = $map_settings;
				$dbData->update_select       = $select;
				$dbData->template_select     = $template_select;
				$dbData->map_typ_select      = $map_typ_select;
				$dbData->map_img_type_select = $map_img_type_select;
				$dbData->l                   = $ajax_msg['templates'];

				try {
					$template               = $this->twig->render( 'admin-settings-by-id.twig', [ 'd' => $dbData ] );
					$responseJson->template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
						'',
						' '
					), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
				} catch ( LoaderError | SyntaxError | RuntimeError $e ) {
					$responseJson->msg = $e->getMessage();
				} catch ( Throwable $e ) {
					$responseJson->msg = $e->getMessage();
				}
				$responseJson->status = true;
				break;

			case 'update_google_rezension':
				$place_id         = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
				$update_option    = filter_input( INPUT_POST, 'update_option', FILTER_SANITIZE_NUMBER_INT );
				$update_interval  = filter_input( INPUT_POST, 'update_interval', FILTER_SANITIZE_NUMBER_INT );
				$google_map_type  = filter_input( INPUT_POST, 'google_map_type', FILTER_SANITIZE_STRING );
				$map_image_format = filter_input( INPUT_POST, 'map_image_format', FILTER_SANITIZE_STRING );
				$static_card_zoom = filter_input( INPUT_POST, 'static_card_zoom', FILTER_SANITIZE_NUMBER_INT );
				filter_input( INPUT_POST, 'map_scale_aktiv', FILTER_SANITIZE_STRING ) ? $scale_aktiv = 1 : $scale_aktiv = 0;
				$horizontal_size = filter_input( INPUT_POST, 'horizontal_size', FILTER_SANITIZE_NUMBER_INT );
				$vertical_size   = filter_input( INPUT_POST, 'vertical_size', FILTER_SANITIZE_NUMBER_INT );


				$nextTime = '';
				if ( $update_option == 1 ) {
					switch ( $update_interval ) {
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

				$update_option == 2 ? $updateInterval = 2 : $updateInterval = $update_interval;
				$record->synchronization_intervall = $updateInterval;
				$static_card_zoom ? $staticZoom = $static_card_zoom : $staticZoom = 15;
				$settings = [
					'map_zoom'        => (int) $staticZoom,
					'map_type'        => esc_html( $google_map_type ),
					'map_image_type'  => esc_html( $map_image_format ),
					'map_scale_aktiv' => $scale_aktiv,
					'horizontal_size' => (int) $horizontal_size,
					'vertical_size'   => (int) $vertical_size,
				];

				$args   = sprintf( 'WHERE place_id="%s"', $place_id );
				$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
				if ( ! $dbData->status ) {
					$responseJson->msg = $errMsg['ajax_error'];

					return $responseJson;
				}

				$dbData               = $dbData->record;
				$dbMapSettings        = json_decode( $dbData->map_settings, true );
				$dbMapSettings        = wp_parse_args( $settings, $dbMapSettings );
				$record->map_settings = json_encode( $dbMapSettings );

				$dbDate  = date( 'y-m-d', strtotime( $dbData->next_synchronization ) );
				$newDate = date( 'Y-m-d', strtotime( $nextTime ) );

				if ( strtotime( $dbDate ) !== strtotime( $newDate ) ) {
					$record->next_synchronization = $nextTime;
					$next_time                    = date( 'd.m.Y H:i:s', strtotime( $nextTime ) );
				} else {
					$record->next_synchronization = $dbData->next_synchronization;
					$next_time                    = $dbData->nextUpdate;
				}

				$update_option == 2 ? $responseJson->next_time = __( 'manual', 'google-rezensionen-api' ) : $responseJson->next_time = $next_time;
				$record->last_update     = $dbData->last_update;
				$record->automatic_aktiv = $update_option;
				$record->place_id        = $place_id;
				apply_filters( $this->basename . '/update_rezension_optionen', $record );
				$responseJson->status         = true;
				$responseJson->id             = $place_id;
				$responseJson->opttion_update = true;

				break;
		}

		return $responseJson;
	}

	public function make_rezension_data( $data ) {
		$response           = new stdClass();
		$address_components = json_decode( $data->address_components );

		$street_number               = 'street_number';
		$route                       = 'route';
		$locality                    = 'locality';
		$sublocality_level_1         = 'sublocality_level_1';
		$administrative_area_level_3 = 'administrative_area_level_3';
		$administrative_area_level_1 = 'administrative_area_level_1';
		$country                     = 'country';
		$postal_code                 = 'postal_code';

		$settings_adresse = [
			'locality'                    => 'long_name',
			'administrative_area_level_1' => 'long_name',
			'postal_code'                 => 'long_name',
			'country'                     => 'long_name',
			'street_number'               => 'long_name',
			'route'                       => 'short_name',
		];


		//TODO ADRESSE

		$adresse = [];

		foreach ( $address_components as $tmp ) {
			if ( in_array( $street_number, $tmp->types ) ) {
				$name           = $settings_adresse['street_number'];
				$adresse['hnr'] = $tmp->$name;
			}
			if ( in_array( $route, $tmp->types ) ) {
				$name              = $settings_adresse['route'];
				$adresse['street'] = $tmp->$name;
			}
			if ( in_array( $postal_code, $tmp->types ) ) {
				$name           = $settings_adresse['postal_code'];
				$adresse['plz'] = $tmp->$name;
			}
			if ( in_array( $locality, $tmp->types ) ) {
				$name             = $settings_adresse['locality'];
				$adresse['stadt'] = $tmp->$name;
			}
			if ( in_array( $administrative_area_level_1, $tmp->types ) ) {
				$name                  = $settings_adresse['administrative_area_level_1'];
				$adresse['bundesland'] = $tmp->$name;
			}
			if ( in_array( $country, $tmp->types ) ) {
				$name            = $settings_adresse['country'];
				$adresse['land'] = $tmp->$name;
			}
		}

		if ( $data->types ) {
			$types      = json_decode( $data->types );
			$place_type = apply_filters( $this->basename . '/api_types', $types[0] );
		} else {
			$place_type = '';
		}

		$num           = str_replace( [ '.', ',' ], '.', $data->user_rating );
		$float         = ( round( $num * 2 ) / 2 );
		$user_rating   = number_format( $float, 1, ',', '' );
		$explodeRating = explode( ',', $user_rating );
		$starNumbers   = $explodeRating[0];
		$explodeRating[1] != 0 ? $starHalf = true : $starHalf = false;

		$star = '';
		for ( $i = 1; $i <= $starNumbers; $i ++ ) {
			$star .= '<i class="star-color fa fa-star"></i> ';
			if ( $i == (int) $explodeRating[0] && $starHalf ) {
				$star .= '<i class="star-color fa fa-star-half"></i> ';
			}
		}

		$data->user_ratings_total == 1 ? $response->lang_rezension = __( 'Rezension', 'google-rezensionen-api' ) : $response->lang_rezension = __( 'Rezensionen', 'google-rezensionen-api' );
		$response->stars       = $star;
		$response->place_type  = $place_type;
		$response->adresse     = $adresse;
		$response->img_url     = GOOGLE_REZENSIONEN_API_UPLOAD_URL . $data->map_image;
		$response->user_rating = number_format( $data->user_rating, 1, ',', '' );

		return $response;
	}

}