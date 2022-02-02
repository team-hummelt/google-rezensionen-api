<?php
namespace Rezensionen\PublicRegister;
use Google_Rezensionen_Api;
use Rezensionen\PublicAjax\Google_Rezensionen_Api_Public_Ajax;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/public
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */
class Google_Rezensionen_Api_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $basename    The ID of this plugin.
	 */
	private string $basename;

	/**
	 * The plugin dir.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_dir    plugin dir Path.
	 */
	protected string $plugin_dir;

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
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name The name of the plugin.
	 * @param string $version    The version of this plugin.
	 *
	 *@since    1.0.0
	 */
	public function __construct( string $plugin_name, string $version, Google_Rezensionen_Api $main ) {

		$this->basename = $plugin_name;
		$this->version = $version;
		$this->main = $main;
		$this->plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->basename;
	}

	/**
	 * Register GOOGLE REZENSIONEN API AJAX NO ADMIN RESPONSE HANDLE
	 *
	 * @since    1.0.0
	 */
	public function prefix_ajax_HupaGooglePublicApiHandle(): void
	{

		check_ajax_referer('bs_form_public_handle');

		/**
		 * The class for defining AJAX in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class_google_rezensionen_api_public_ajax.php';
		$publicAjaxHandle = new Google_Rezensionen_Api_Public_Ajax($this->basename, $this->main);
		wp_send_json($publicAjaxHandle->google_rezensionen_api_public_ajax_handle());
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Google_Rezensionen_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Google_Rezensionen_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->basename, plugin_dir_url( __FILE__ ) . 'css/google-rezensionen-api-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Google_Rezensionen_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Google_Rezensionen_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->basename, plugin_dir_url( __FILE__ ) . 'js/google-rezensionen-api-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_style( $this->basename . '-public-icons-style', plugins_url( $this->basename ) . '/admin/css/font-awesome.css', array(), $this->version, false );
	}

}
