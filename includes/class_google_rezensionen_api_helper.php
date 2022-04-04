<?php

namespace Rezensionen\Helper;
defined( 'ABSPATH' ) or die();

use Exception;
use Google_Rezensionen_Api;

use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;

/**
 * The BS-Formular2 Helper Class.
 *
 * @since      1.0.0
 * @package    Bs_Formular2
 * @subpackage Bs_Formular2/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Google_Rezensionen_Api_Helper {

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
	 * @param string $basename
	 * @param Google_Rezensionen_Api $main
	 */
	public function __construct( string $basename, Google_Rezensionen_Api $main ) {
		$this->basename = $basename;
		$this->main     = $main;
	}

	/**
	 * @param $array
	 *
	 * @return object
	 * @since 1.0.0
	 */
	final public function goggleApiArrayToObject( $array ): object {
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$array[ $key ] = self::goggleApiArrayToObject( $value );
			}
		}

		return (object) $array;
	}

	/**
	 * @param string|null $args
	 *
	 * @return string
	 * @access  final public
	 * @throws Exception
	 */
	final public function google_api_random_string( string $args = null ): string {
		if ( function_exists( 'random_bytes' ) ) {
			$bytes = random_bytes( 16 );
			$str   = bin2hex( $bytes );
		} elseif ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			$bytes = openssl_random_pseudo_bytes( 16 );
			$str   = bin2hex( $bytes );
		} else {
			$str = md5( uniqid( 'wp_bs_formulare', true ) );
		}

		return $str;
	}

	/**
	 * @param int $passwordlength
	 * @param int $numNonAlpha
	 * @param int $numNumberChars
	 * @param bool $useCapitalLetter
	 *
	 * @return string
	 * @access final public
	 */
	public function getGoogleApi2GenerateRandomId( int $passwordlength = 12, int $numNonAlpha = 1, int $numNumberChars = 4, bool $useCapitalLetter = true ): string {
		$numberChars = '123456789';
		//$specialChars = '!$&?*-:.,+@_';
		$specialChars = '!$%&=?*-;.,+~@_';
		$secureChars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
		$stack        = $secureChars;
		if ( $useCapitalLetter ) {
			$stack .= strtoupper( $secureChars );
		}
		$count = $passwordlength - $numNonAlpha - $numNumberChars;
		$temp  = str_shuffle( $stack );
		$stack = substr( $temp, 0, $count );
		if ( $numNonAlpha > 0 ) {
			$temp  = str_shuffle( $specialChars );
			$stack .= substr( $temp, 0, $numNonAlpha );
		}
		if ( $numNumberChars > 0 ) {
			$temp  = str_shuffle( $numberChars );
			$stack .= substr( $temp, 0, $numNumberChars );
		}

		return str_shuffle( $stack );
	}

    public function generate_api_config_key():string {
        $updateKey = strtoupper($this->getGoogleApi2GenerateRandomId(24, 0, 8));
        $updateKey = chunk_split($updateKey, 6, '-');
        return substr($updateKey,0,27);
    }

	/**
	 * @param float $bytes
	 *
	 * @return string
	 * @access final public
	 */
	final public function googleApi2FileSizeConvert( float $bytes ): string {
		$result  = '';
		$arBytes = array(
			0 => array( "UNIT" => "TB", "VALUE" => pow( 1024, 4 ) ),
			1 => array( "UNIT" => "GB", "VALUE" => pow( 1024, 3 ) ),
			2 => array( "UNIT" => "MB", "VALUE" => pow( 1024, 2 ) ),
			3 => array( "UNIT" => "KB", "VALUE" => 1024 ),
			4 => array( "UNIT" => "B", "VALUE" => 1 ),
		);

		foreach ( $arBytes as $arItem ) {
			if ( $bytes >= $arItem["VALUE"] ) {
				$result = $bytes / $arItem["VALUE"];
				$result = str_replace( ".", ",", strval( round( $result, 2 ) ) ) . " " . $arItem["UNIT"];
				break;
			}
		}

		return $result;
	}

	/**
	 * @param $file_post
	 *
	 * @return array
	 */
	public function google_api_re_array_object( $file_post ): array {
		$file_ary   = array();
		$file_count = count( $file_post['name'] );
		$file_keys  = array_keys( $file_post );

		for ( $i = 0; $i < $file_count; $i ++ ) {
			foreach ( $file_keys as $key ) {
				$file_ary[ $i ][ $key ] = $file_post[ $key ][ $i ];
			}
		}

		return $file_ary;
	}

	/**
	 * @param $dir
	 *
	 * @return bool
	 */
	public function googleApiDestroyDir( $dir ): bool {
		if ( ! is_dir( $dir ) || is_link( $dir ) ) {
			return unlink( $dir );
		}

		foreach ( scandir( $dir ) as $file ) {
			if ( $file == "." || $file == ".." ) {
				continue;
			}
			if ( ! $this->googleApiDestroyDir( $dir . "/" . $file ) ) {
				chmod( $dir . "/" . $file, 0777 );
				if ( ! $this->googleApiDestroyDir( $dir . "/" . $file ) ) {
					return false;
				}
			}
		}

		return rmdir( $dir );
	}


	/**
	 * @throws Exception
	 */
	public function google_api_recursive_copy( $src, $dst ) {

		$dir = opendir( $src );

		if ( ! is_dir( $dst ) ) {
			if ( ! mkdir( $dst ) ) {
				throw new Exception( 'Destination Ordner nicht gefunden gefunden.' );
			}
		}
		while ( ( $file = readdir( $dir ) ) ) {
			if ( ( $file != '.' ) && ( $file != '..' ) ) {
				if ( is_dir( $src . DIRECTORY_SEPARATOR . $file ) ) {
					$this->google_api_recursive_copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
				} else {
					copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
				}
			}
		}
		closedir( $dir );
	}

	/**
	 * @throws Exception
	 */
	function google_api_move_file( $file, $to, $unlink = false ) {
		if ( ! copy( $file, $to ) ) {
			if ( $unlink ) {
				if ( ! unlink( $file ) ) {
					throw new Exception( 'File konnte nicht gelöscht gefunden.' );
				}
			}
			throw new Exception( 'File konnte nicht kopiert werden.' );
		}
	}

	public function google_api_get_menu_svg_icon( $why ): string {
		$icon = '';
		switch ( $why ) {
			case 'google':
				$svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#db4437" class="google-api-logo" viewBox="0 0 24 24">
  						 <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
						 </svg>';
				$icon = $this->google_api_base64_decode_encode( $svg, 'encode' );
				break;
			case 'extension':
				$svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="#1673aa" class="bi bi-subtract" viewBox="0 0 21 21"> <path d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2z"></path> </svg>';
				$icon = $this->google_api_base64_decode_encode( $svg, 'encode' );
				break;
		}

		return 'data:image/svg+xml;base64,' . $icon;
	}

	/**
	 * @param $data
	 * @param $method
	 *
	 * @return false|string
	 */
	public function google_api_base64_decode_encode( $data, $method ) {

		switch ( $method ) {
			case'encode':
				return base64_encode( $data );
			case'decode':
				return base64_decode( $data );
			default:
				return '';
		}
	}

	/**
	 * @param null $args
	 *
	 * @return array
	 */
	public function google_api_selects( $args = null ): array {
		$select = [
			'user_role'        => [
				'read'           => esc_html__( 'Subscriber', 'google-rezensionen-api' ),
				'edit_posts'     => esc_html__( 'Contributor', 'google-rezensionen-api' ),
				'publish_posts'  => esc_html__( 'Author', 'google-rezensionen-api' ),
				'publish_pages'  => esc_html__( 'Editor', 'google-rezensionen-api' ),
				'manage_options' => esc_html__( 'Administrator', 'google-rezensionen-api' )
			],
			'map_type_select'  => [
				'roadmap'   => __( 'Roadmap', 'google-rezensionen-api' ),
				'satellite' => __( 'Satellite', 'google-rezensionen-api' ),
				'hybrid'    => __( 'Hybrid', 'google-rezensionen-api' ),
				'terrain'   => __( 'Terrain', 'google-rezensionen-api' ),
			],
			'map_image_format' => [
				'png'   => 'PNG',
				'jpg'   => 'JPG',
				'png32' => 'PNG32',
				'gif'   => 'GIF',

			],
			'update_time_select' => [
				'1'   => __( 'daily', 'google-rezensionen-api' ),
				'2'   => __( 'weekly', 'google-rezensionen-api' ),
				'3' => __( 'monthly', 'google-rezensionen-api' ),
			],
			'ausgabe_template_select' => [
				'1'   => __( 'very big (xxl)', 'google-rezensionen-api' ),
				'2'   => __( 'big (xl)', 'google-rezensionen-api' ),
				'3'   => __( 'medium (md)', 'google-rezensionen-api' ),
				'4' => __( 'small (sm)', 'google-rezensionen-api' ),
				'5' => __( 'extra small (xs)', 'google-rezensionen-api' ),
			],
		];

		if ( $args ) {
			return $select[ $args ];
		}

		return $select;
	}

	public function google_api_types(string $type = '')
	{
		//First Types
		$types = [
			//Buchhaltung
			'accounting'              =>  __( 'accounting', 'google-rezensionen-api' ),
			//Flughafen
			'airport'                 => __( 'airport', 'google-rezensionen-api' ),
			//Vergnügungspark
			'amusement_park'          => __( 'amusement park', 'google-rezensionen-api' ),
			//Aquarium
			'aquarium'                => __( 'aquarium', 'google-rezensionen-api' ),
			//Kunstgalerie
			'art_gallery'             => __( 'art gallery', 'google-rezensionen-api' ),
			//Geldautomat
			'atm'                     => __( 'atm', 'google-rezensionen-api' ),
			//Bäckerei
			'bakery'                  => __( 'bakery', 'google-rezensionen-api' ),
			//Bank
			'bank'                    => __( 'bank', 'google-rezensionen-api' ),
			//Bar
			'bar'                     => __( 'bar', 'google-rezensionen-api' ),
			//Schönheitssalon
			'beauty_salon'            => __( 'beauty salon', 'google-rezensionen-api' ),
			//Fahrradgeschäft
			'bicycle_store'           => __( 'bicycle store', 'google-rezensionen-api' ),
			//Buchhandlung
			'book_store'              => __( 'book store', 'google-rezensionen-api' ),
			//Bowlingbahn
			'bowling_alley'           => __( 'bowling alley', 'google-rezensionen-api' ),
			//Busbahnhof
			'bus_station'             => __( 'bus station', 'google-rezensionen-api' ),
			//Cafe
			'cafe'                    => __( 'cafe', 'google-rezensionen-api' ),
			//Zeltplatz
			'campground'              => __( 'campground', 'google-rezensionen-api' ),
			//Autohändler
			'car_dealer'              => __( 'car dealer', 'google-rezensionen-api' ),
			//Autovermietung
			'car_rental'              => __( 'car rental', 'google-rezensionen-api' ),
			//Autoreparatur
			'car_repair'              => __( 'car repair', 'google-rezensionen-api' ),
			//Autowaschanlage
			'car_wash'                => __( 'car wash', 'google-rezensionen-api' ),
			//Casino
			'casino'                  => __( 'casino', 'google-rezensionen-api' ),
			//Friedhof
			'cemetery'                => __( 'cemetery', 'google-rezensionen-api' ),
			//Kirche
			'church'                  => __( 'church', 'google-rezensionen-api' ),
			//Stadthalle
			'city_hall'               => __( 'city hall', 'google-rezensionen-api' ),
			//Bekleidungsgeschäft
			'clothing_store'          => __( 'clothing store', 'google-rezensionen-api' ),
			//Lebensmittelgeschäft
			'convenience_store'       => __( 'convenience store', 'google-rezensionen-api' ),
			//Gerichtsgebäude
			'courthouse'              => __( 'courthouse', 'google-rezensionen-api' ),
			//Zahnarzt
			'dentist'                 => __( 'dentist', 'google-rezensionen-api' ),
			//Kaufhaus
			'department_store'        => __( 'department store', 'google-rezensionen-api' ),
			//Doktor
			'doctor'                  => __( 'doctor', 'google-rezensionen-api' ),
			//Drogeriemarkt
			'drugstore'               => __( 'drugstore', 'google-rezensionen-api' ),
			//Elektriker
			'electrician'             => __( 'electrician', 'google-rezensionen-api' ),
			//Elektrofachmarkt
			'electronics_store'       => __( 'electronics store', 'google-rezensionen-api' ),
			//Botschaft
			'embassy'                 => __( 'embassy', 'google-rezensionen-api' ),
			//Feuerwache
			'fire_station'            => __( 'fire station', 'google-rezensionen-api' ),
			//Florist
			'florist'                 => __( 'florist', 'google-rezensionen-api' ),
			//Bestattungsinstitut
			'funeral_home'            => __( 'funeral home', 'google-rezensionen-api' ),
			//Möbelhaus
			'furniture_store'         => __( 'furniture store', 'google-rezensionen-api' ),
			//Tankstelle
			'gas_station'             => __( 'gas station', 'google-rezensionen-api' ),
			//Fitnessstudio
			'gym'                     => __( 'gym', 'google-rezensionen-api' ),
			//Haarpflege
			'hair_care'               => __( 'hair care', 'google-rezensionen-api' ),
			//Baumarkt
			'hardware_store'          => __( 'hardware store', 'google-rezensionen-api' ),
			//Hindu-Tempel
			'hindu_temple'            => __( 'hindu temple', 'google-rezensionen-api' ),
			//Haushaltswarengeschäft
			'home_goods_store'        => __( 'home goods store', 'google-rezensionen-api' ),
			//Krankenhaus
			'hospital'                => __( 'hospital', 'google-rezensionen-api' ),
			//Versicherungsagentur
			'insurance_agency'        => __( 'insurance agency', 'google-rezensionen-api' ),
			//Juweliergeschäft
			'jewelry_store'           => __( 'jewelry store', 'google-rezensionen-api' ),
			//Wäscherei
			'laundry'                 => __( 'laundry', 'google-rezensionen-api' ),
			//Rechtsanwalt
			'lawyer'                  => __( 'lawyer', 'google-rezensionen-api' ),
			//Bibliothek
			'library'                 => __( 'library', 'google-rezensionen-api' ),
			//Stadtbahnstation
			'light_rail_station'      => __( 'light rail station', 'google-rezensionen-api' ),
			//Spirituosengeschäft
			'liquor_store'            => __( 'liquor store', 'google-rezensionen-api' ),
			//lokale Regierungsstelle
			'local_government_office' => __( 'local government office', 'google-rezensionen-api' ),
			//Schlüsseldienst
			'locksmith'               => __( 'locksmith', 'google-rezensionen-api' ),
			//Unterbringung
			'lodging'                 => __( 'lodging', 'google-rezensionen-api' ),
			//Essenslieferung
			'meal_delivery'           => __( 'meal delivery', 'google-rezensionen-api' ),
			//Essen zum Mitnehmen
			'meal_takeaway'           => __( 'meal takeaway', 'google-rezensionen-api' ),
			//Moschee
			'mosque'                  => __( 'mosque', 'google-rezensionen-api' ),
			//Filmverleih
			'movie_rental'            => __( 'movie rental', 'google-rezensionen-api' ),
			//Kino
			'movie_theater'           => __( 'movie theater', 'google-rezensionen-api' ),
			//Umzugsunternehmen
			'moving_company'          => __( 'moving company', 'google-rezensionen-api' ),
			//Museum
			'museum'                  => __( 'museum', 'google-rezensionen-api' ),
			//Nachtclub
			'night_club'              => __( 'night club', 'google-rezensionen-api' ),
			//Maler
			'painter'                 => __( 'painter', 'google-rezensionen-api' ),
			//Park
			'park'                    => __( 'Park', 'google-rezensionen-api' ),
			//Parkplatz
			'parking'                 => __( 'parking', 'google-rezensionen-api' ),
			//Zoohandlung
			'pet_store'               => __( 'pet store', 'google-rezensionen-api' ),
			//Apotheke
			'pharmacy'                => __( 'pharmacy', 'google-rezensionen-api' ),
			//Physio-Therapeut
			'physiotherapist'         => __( 'physiotherapist', 'google-rezensionen-api' ),
			//Klempner
			'plumber'                 => __( 'plumber', 'google-rezensionen-api' ),
			//Polizei
			'police'                  => __( 'police', 'google-rezensionen-api' ),
			//Postamt
			'post_office'             => __( 'post office', 'google-rezensionen-api' ),
			//Grundschule
			'primary_school'          => __( 'primary school', 'google-rezensionen-api' ),
			//Immobilienbüro
			'real_estate_agency'      => __( 'real estate agency', 'google-rezensionen-api' ),
			//Restaurant
			'restaurant'              => __( 'restaurant', 'google-rezensionen-api' ),
			//Dachdeckerbetrieb
			'roofing_contractor'      => __( 'roofing contractor', 'google-rezensionen-api' ),
			//Wohnmobilstellplatz
			'rv_park'                 => __( 'rv park', 'google-rezensionen-api' ),
			//Schule
			'school'                  => __( 'school', 'google-rezensionen-api' ),
			//Sekundarschule
			'secondary_school'        => __( 'secondary school', 'google-rezensionen-api' ),
			//Schuhgeschäft
			'shoe_store'              => __( 'shoe store', 'google-rezensionen-api' ),
			//Einkaufspassage
			'shopping_mall'           => __( 'shopping mall', 'google-rezensionen-api' ),
			//Heilbad
			'spa'                     => __( 'spa', 'google-rezensionen-api' ),
			//Stadion
			'stadium'                 => __( 'stadium', 'google-rezensionen-api' ),
			//Lagerung
			'storage'                 => __( 'storage', 'google-rezensionen-api' ),
			//Shop
			'store'                   => __( 'store', 'google-rezensionen-api' ),
			//U-Bahn-Station
			'subway_station'          => __( 'subway station', 'google-rezensionen-api' ),
			//Supermarkt
			'supermarket'             => __( 'supermarket', 'google-rezensionen-api' ),
			//Synagoge
			'synagogue'               => __( 'synagogue', 'google-rezensionen-api' ),
			//Taxistand
			'taxi_stand'              => __( 'taxi stand', 'google-rezensionen-api' ),
			//Touristenattraktion
			'tourist_attraction'      => __( 'tourist attraction', 'google-rezensionen-api' ),
			//Bahnhof
			'train_station'           => __( 'train station', 'google-rezensionen-api' ),
			//Bahnhof
			'transit_station'         => __( 'transit station', 'google-rezensionen-api' ),
			//Reisebüro
			'travel_agency'           => __( 'travel agency', 'google-rezensionen-api' ),
			//Universität
			'university'              => __( 'university', 'google-rezensionen-api' ),
			//tierärztliche Betreuung
			'veterinary_care'         => __( 'veterinary care', 'google-rezensionen-api' ),
			//zoo
			'zoo'                     => __( 'zoo', 'google-rezensionen-api' ),
		];

		if($type){
            if(!isset($types[$type])){
                return '';
            }else {
                return $types[$type];
            }
		}
		return $types;
	}

	public function get_hupa_countries_select() {
		$code         = substr( get_bloginfo( 'language' ), 0, 2 );
		$checkCountry = apply_filters( $this->basename . '/check_countries', $code );

		return apply_filters( $this->basename . '/get_countries', '', true, $checkCountry . ",code" );
	}
}