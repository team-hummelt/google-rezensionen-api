<?php

namespace Rezensionen\PublicAjax;
defined('ABSPATH') or die();


use Google_Rezensionen_Api;
use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use stdClass;

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

class Google_Rezensionen_Api_Public_Ajax {
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
	 * @var      string    $plugin_dir  plugin Slug Path.
	 */
	protected string $plugin_dir;

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
	 * The Version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current Version of this plugin.
	 */
	private string $version;

	/**
	 * The ADMIN AJAX DATA
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array|object $data The ADMIN AJAX DATA.
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
	 */
	public function __construct(string $basename, Google_Rezensionen_Api $main)
	{
		$this->basename = $basename;
		$this->plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->basename . DIRECTORY_SEPARATOR;
		$this->method = '';
		if (isset($_POST['daten'])) {
			$this->data = $_POST['daten'];
			$this->method = filter_var($this->data['method'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		}

		if (!$this->method) {
			$this->method = $_POST['method'];
		}

		$this->main = $main;
	}

	/**
	 * GOOGLE REZENSIONEN API Public AJAX RESPONSE.
	 * @return stdClass
	 *@since    1.0.0
	 */
	public function google_rezensionen_api_public_ajax_handle(): stdClass {
		global $wpdb;
		$record = new stdClass();
		$responseJson = new stdClass();
		switch ($this->method) {
			case'test':

				break;
		}

		return $responseJson;
	}
}