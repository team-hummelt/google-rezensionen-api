<?php

namespace Rezensionen\Shortcode;
defined( 'ABSPATH' ) or die();

use Exception;
use Google_Rezensionen_Api;

use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * The BS-Formular2 Shortcode Class.
 *
 * @since      1.0.0
 * @package    Bs_Formular2
 * @subpackage Bs_Formular2/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Google_Rezensionen_Shortcode {

	private static $instance;

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
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var Google_Rezensionen_Api $main The main class.
	 */
	private Google_Rezensionen_Api $main;

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
	 * @param string $plugin_name
	 * @param string $version
	 * @param Google_Rezensionen_Api $main
	 * @param Environment $twig
	 */
	public function __construct( string $plugin_name, string $version, Google_Rezensionen_Api $main, Environment $twig ) {

		$this->basename = $plugin_name;
		$this->version  = $version;
		$this->main     = $main;
		$this->twig = $twig;
		add_shortcode( 'google_rezension', array( $this, 'google_rezension_shortcode' ) );
	}

	/**
	 * @param string $plugin_name
	 * @param string $version
	 * @param Google_Rezensionen_Api $main
	 * @param Environment $twig
	 *
	 * @return static
	 */
	public static function instance(string $plugin_name,string $version,Google_Rezensionen_Api $main, Environment $twig ): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_name, $version, $main , $twig);
		}

		return self::$instance;
	}

	public function google_rezension_shortcode( $atts, $content, $tag ) {

		$a = shortcode_atts( array(
			'id'=> '',
			'bg' => '#f2f2f2',
			'template' => '3',
			'class' => ''
		), $atts );

		ob_start();
		$args   = sprintf( 'WHERE place_id="%s"', $a['id'] );
		$dbData = apply_filters( $this->basename . '/get_api_rezension', $args, false );
		if(!$dbData->status){
			return '';
		}
		$dbData = $dbData->record;
		if(!$dbData->aktiv){
			return '';
		}
		$lang = $this->get_theme_default_settings( 'ajax_msg' );
		$edit = $this->make_shortcode_rezension_data($dbData);
		$dbData->stars          = $edit->stars;
		$dbData->place_type     = $edit->place_type;
		$dbData->adresse        = $edit->adresse;
		$dbData->img_url        = $edit->img_url;
		$dbData->user_rating    = $edit->user_rating;
		$dbData->lang_rezension = $edit->lang_rezension;
		$dbData->google_url     = urlencode( $dbData->name );
		$dbData->bg_color    = $a['bg'];
		$dbData->l = $lang['templates'];
		$dbData->extraClass = $a['class'];

		switch ($a['template']){
			case '1':
					$template = 'shortcode-template-xxl.twig';
				break;
			case'2':
				$template = 'shortcode-template-xl.twig';
				break;
			case'3':
				$template = 'shortcode-template-md.twig';
				break;
			case'4':
				$template = 'shortcode-template-sm.twig';
				break;
			case'5':
				$template = 'shortcode-template-xs.twig';
				break;
			default:
				$template = 'shortcode-template-md.twig';
		}

		$dbData->extraClass = $a['class'];
		$tempDir = plugin_dir_path(dirname(__FILE__)) . 'includes' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'shortcode' . DIRECTORY_SEPARATOR;
		$twig_loader = new FilesystemLoader($tempDir);
		$twig = new Environment($twig_loader);
		try {
			$template  = $twig->render( $template, [ 'd' => $dbData ] );
			$template = preg_replace( array( '/<!--(.*)-->/Uis', "/[[:blank:]]+/" ), array(
				'',
				' '
			), str_replace( array( "\n", "\r", "\t" ), '', $template ) );
		} catch ( LoaderError | SyntaxError | RuntimeError | Throwable $e ) {
			return '';
		}
		echo $template;
		return ob_get_clean();
	}

	private function make_shortcode_rezension_data( $data ): stdClass {
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
			$star .= '<i class="star-color fa fa-star"></i>';
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