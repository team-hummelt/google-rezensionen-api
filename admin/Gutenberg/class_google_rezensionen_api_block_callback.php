<?php

namespace Rezensionen\Endpoints;
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
class Google_Rezensionen_Api_Block_Callback {

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

	public static function callback_google_rezensionen_api( $attributes ) {

		if ( $attributes ) {
			ob_start();
			isset( $attributes['className'] ) ? $class = $attributes['className'] : $class = '';
			isset( $attributes['selectedTemplate'] ) && ! empty( $attributes['selectedTemplate']) ? $template = $attributes['selectedTemplate'] : $template = '';
			isset( $attributes['selectedRezension'] ) && ! empty( $attributes['selectedRezension']) ? $selectedRezension = $attributes['selectedRezension'] : $selectedRezension = '';
			isset( $attributes['backgroundColor'] ) && ! empty( $attributes['backgroundColor']) ? $backgroundColor = $attributes['backgroundColor'] : $backgroundColor = '';
			echo do_shortcode( '[google_rezension id="' . $selectedRezension . '" bg="' . $backgroundColor . '" template="'.$template.'" class="'.$class.'"]');
			return ob_get_clean();
		}
	}
}