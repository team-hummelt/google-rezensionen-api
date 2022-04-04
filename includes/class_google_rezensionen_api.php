<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 */


use Goggle\Rezension\Create_Wwdh_Extension_Database;
use Goggle\Rezension\WWDH_Extension_API;
use Goggle\Rezension\Wwdh_Extension_Helper;
use Goggle\Rezension\WWDH_Extension_Table;
use Goggle\Rezension\Wwdh_Public_Api;
use GoogleRezension\Extensions\GoogleRezensionExtensions;
use Hupa\RezensionenApi\Google_Rezensionen_Api_Database;
use Rezensionen\AdminRegister\Google_Rezensionen_Api_Admin;
use Rezensionen\ApiCurlHandle\Google_Rezensionen_Api_Curl_Handle;
use Rezensionen\Endpoints\Google_Rezensionen_Api_Block_Callback;
use Rezensionen\Endpoints\Google_Rezensionen_Api_Rest_Endpoint;
use Rezensionen\Helper\Google_Rezensionen_Api_Helper;
use Rezensionen\PublicRegister\Google_Rezensionen_Api_Public;
use Rezensionen\Shortcode\Google_Rezensionen_Shortcode;
use Rezensionen\SrvApi\Endpoint\Srv_Api_Enpoint;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */
class Google_Rezensionen_Api
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Google_Rezensionen_Api_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected Google_Rezensionen_Api_Loader $loader;

    /**
     * The Public API ID_RSA.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $id_rsa plugin API ID_RSA.
     */
    private string $id_rsa;

    /**
     * The PLUGIN API ID_RSA.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $id_plugin_rsa plugin API ID_RSA.
     */
    private string $id_plugin_rsa;

    /**
     * The PLUGIN API ID_RSA.
     *
     * @since    1.0.0
     * @access   private
     * @var      object $plugin_api_config plugin API ID_RSA.
     */
    private object $plugin_api_config;


    /**
     * The Public API DIR.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $api_dir plugin API DIR.
     */
    private string $api_dir;

    /**
     * The Public Extension DIR.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $twig_user_templates plugin Extension DIR.
     */
    private string $twig_user_templates;


    /**
     * The Public Extension Preview DIR.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $extension_preview plugin Extension Preview DIR.
     */
    private string $extension_preview;

    /**
     * The Public Extension Preview DIR.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $extension_dir plugin Extension Preview DIR.
     */
    private string $extension_dir;


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
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected string $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected string $version;

    /**
     * The current database version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $db_version The current database version of the plugin.
     */
    protected string $db_version;

    /**
     * The settings id for settings table of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      int $settings_id The settings id for settings table.
     */
    protected int $settings_id;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @var object The main class.
     */
    public object $main;

    /**
     * The plugin Slug Path.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_slug plugin Slug Path.
     */
    private string $plugin_slug;

    /**
     * The plugin Slug Path.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $api_curl_dir plugin Slug Path.
     */
    private string $api_curl_dir;

    /**
     * The plugin Slug Path.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $srv_api_dir plugin Slug Path.
     */
    private string $srv_api_dir;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();

        if (defined('GOOGLE_REZENSIONEN_API_VERSION')) {
            $this->version = GOOGLE_REZENSIONEN_API_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        if (defined('GOOGLE_REZENSIONEN_API_DB_VERSION')) {
            $this->db_version = GOOGLE_REZENSIONEN_API_DB_VERSION;
        } else {
            $this->db_version = '1.0.0';
        }

        if (defined('GOOGLE_REZENSIONEN_API_SETTINGS_ID')) {
            $this->settings_id = GOOGLE_REZENSIONEN_API_SETTINGS_ID;
        } else {
            $this->settings_id = 1;
        }

        $this->plugin_name = GOOGLE_REZENSIONEN_API_BASENAME;
        $this->plugin_slug = GOOGLE_REZENSIONEN_API_SLUG_PATH;
        $this->api_curl_dir = GOOGLE_REZENSIONEN_API_CURL_DIR;
        $this->main = $this;

        //JOB EXTENSION
        if (is_file(GOOGLE_REZENSION_ID_RSA_DIR . 'public_id_rsa')) {
            $id_rsa = file_get_contents(GOOGLE_REZENSION_ID_RSA_DIR . 'public_id_rsa');
            $this->id_rsa = base64_encode($id_rsa);
        } else {
            $this->id_rsa = '';
        }



        //JOB SRV API
        $this->srv_api_dir = plugin_dir_path(dirname(__FILE__)) . 'admin' . DIRECTORY_SEPARATOR .'srv-api' . DIRECTORY_SEPARATOR;

        if (is_file($this->srv_api_dir  . 'id_rsa' . DIRECTORY_SEPARATOR . $this->plugin_name.'_id_rsa')) {
            $this->id_plugin_rsa = base64_encode($this->srv_api_dir . DIRECTORY_SEPARATOR . 'id_rsa' . $this->plugin_name.'_id_rsa');
        } else {
            $this->id_plugin_rsa = '';
        }
        if (is_file($this->srv_api_dir  . 'config' . DIRECTORY_SEPARATOR . 'config.json')) {
            $this->plugin_api_config = json_decode( file_get_contents( $this->srv_api_dir  . 'config' . DIRECTORY_SEPARATOR . 'config.json'));
        } else {
            $this->plugin_api_config = (object) [];
        }


        $this->extension_preview = GOOGLE_REZENSION_EXTENSION_PREVIEW_DIR;
        $this->extension_dir = GOOGLE_REZENSION_EXTENSION_DIR;
        $this->api_dir = GOOGLE_REZENSION_EXTENSION_API_DIR;

        //Check PHP AND WordPress Version
        $this->check_dependencies();
        $this->load_dependencies();
        $this->set_locale();
        $this->google_api_rezensionen_database();
        //JOB EXTENSION


        $tempDir = plugin_dir_path(dirname(__FILE__)) . 'includes' . DIRECTORY_SEPARATOR . 'templates';
        $twig_loader = new FilesystemLoader($tempDir);
        $this->twig = new Environment($twig_loader);


        //JOB EXTENSION
        $this->google_rezensionen_extension_options();
        $this->register_extension_helper_class();
        $this->wwdh_public_api();

        $this->wwdh_extension_database();
        $this->experience_reports_extension_database();
        $this->wwdh_extension_api();


        $this->google_api_helper();
        $this->register_rezension_gutenberg_callback();
        $this->register_api_editor_rest_api_routes();

        $this->define_admin_hooks();
        $this->define_public_hooks();

        $this->define_google_api_curl_handle();
        $this->define_google_api_shortcodes();
        $this->define_google_api_classic_widget();
        $this->register_srv_rest_api_routes();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     * - Google_Rezensionen_Api_Defaults_Trait. The trait for the default settings of the BS-Formular2
     * - Google_Rezensionen_Api_Loader. Orchestrates the hooks of the plugin.
     * - Google_Rezensionen_Api_i18n. Defines internationalization functionality.
     * - Google_Rezensionen_Api_Admin. Defines all hooks for the admin area.
     * - Google_Rezensionen_Api_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The trait for the default settings of the Google_Rezensionen_Api
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/Traits/Google_Rezensionen_Api_Defaults_Trait.php';

        /**
         * The trait for database of the Google_Rezensionen_Api
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/database/class_google_rezensionen_api_database.php';

        //JOB EXTENSION START
        /**
         * The trait for the default settings of the BS-Formular2
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/api/api-classes/trait_extension_defaults.php';


        /**
         * The trait for database of the Google_Rezensionen_Api
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/api/database/class_create_wwdh_extension_database.php';


        /**
         * The trait for database of the Google_Rezensionen_Api
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/api/database/class_wwdh_extension_table.php';

        /**
         * The class for the Extension Options of the Wp_Experience_Reports
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/class-google-rezension-extensions.php';

        /**
         * The class for the Extension Options of the Wp_Experience_Reports
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/class-google-rezension-extensions.php';

        /**
         * The class responsible for defining all actions of the Public API.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/api/api-classes/class_wwdh_public_api.php';

        /**
         * The class for the Extension Options of the Wp_Experience_Reports
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/class-wwdh-extension-helper.php';

        /**
         * The class responsible for defining all actions of the Extension API.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'extensions/api/api-classes/class_wwdh_extension_api.php';


        //JOB EXTENSION END

        /**
         * The Helper Class for defining of the Google_Rezensionen_Api
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class_google_rezensionen_api_helper.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class_google_rezensionen_api_i18n.php';


        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class_google_rezensionen_api_admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_google_rezensionen_api_public.php';

        /**
         * The class responsible for defining all actions of the GOOGLE  API HANDLE.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/api/class_google_rezensionen_api_curl_handle.php';

        /**
         * TWIG autoload for PHP-Template-Engine
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/Twig/autoload.php';

        /**
         * The class responsible for defining WP REST API Routes
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/Gutenberg/class_google_rezensionen_api_rest_endpoint.php';

        /**
         * The class responsible for defining WP REST API CALLBACK
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/Gutenberg/class_google_rezensionen_api_block_callback.php';

        /**
         * Shortcode Class
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class_google_rezensionen_shortcode.php';

        /**
         * WP_WIDGET Class
         * core plugin.
         */
        //require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classic-widget/class_google_rezension_api_widget.php';


        //JOB SRV API Endpoint
        /**
         * SRV API Endpoint
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/srv-api/class_srv_api_enpoint.php';


        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class_google_rezensionen_api_loader.php';

        $this->loader = new Google_Rezensionen_Api_Loader();

    }

    /**
     * Check PHP and WordPress Version
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function check_dependencies(): void
    {
        global $wp_version;
        if (version_compare(PHP_VERSION, GOOGLE_REZENSIONEN_API_PHP_VERSION, '<') || $wp_version < GOOGLE_REZENSIONEN_API_WP_VERSION) {
            $this->maybe_self_deactivate();
        }
    }

    /**
     * Self-Deactivate
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function maybe_self_deactivate(): void
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        deactivate_plugins($this->plugin_slug);
        add_action('admin_notices', array($this, 'self_deactivate_notice'));
    }

    /**
     * Self-Deactivate Admin Notiz
     * of the plugin.
     *
     * @since    1.0.0
     * @access   public
     */
    public function self_deactivate_notice(): void
    {
        echo sprintf('<div class="error" style="margin-top:5rem"><p>' . __('This plugin has been disabled because it requires a PHP version greater than %s and a WordPress version greater than %s. Your PHP version can be updated by your hosting provider.', 'google-rezensionen-api') . '</p></div>', GOOGLE_REZENSIONEN_API_PHP_VERSION, GOOGLE_REZENSIONEN_API_WP_VERSION);
        exit();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Google_Rezensionen_Api_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Google_Rezensionen_Api_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function google_api_rezensionen_database()
    {

        global $google_api_database;
        $google_api_database = new Google_Rezensionen_Api_Database($this->get_db_version(), $this->get_settings_id(), $this->main);

        $this->loader->add_action('init', $google_api_database, 'update_create_google_rezensionen_api_database');
        $this->loader->add_filter($this->plugin_name . '/get_settings', $google_api_database, 'getHupaGoogleRezensionenApiSettings');
        $this->loader->add_action($this->plugin_name . '/set_settings', $google_api_database, 'setHupaGoogleRezensionenApiSettings', 10, 2);
        $this->loader->add_action($this->plugin_name . '/update_settings', $google_api_database, 'updateHupaGoogleRezensionenApiSettings', 10, 2);

        $this->loader->add_action($this->plugin_name . '/get_countries', $google_api_database, 'get_google_api_countries_by_args', 10, 3);
        $this->loader->add_action($this->plugin_name . '/check_countries', $google_api_database, 'google_api_check_countries');

        $this->loader->add_action($this->plugin_name . '/set_api_rezension', $google_api_database, 'set_google_api_rezension');
        $this->loader->add_action($this->plugin_name . '/update_api_rezension', $google_api_database, 'update_google_api_rezension');
        $this->loader->add_action($this->plugin_name . '/get_api_rezension', $google_api_database, 'get_google_api_rezension', 10, 2);
        $this->loader->add_action($this->plugin_name . '/update_static_image', $google_api_database, 'update_google_api_static_image');

        $this->loader->add_action($this->plugin_name . '/set_rezension_field', $google_api_database, 'set_google_api_field');
        $this->loader->add_action($this->plugin_name . '/delete_rezension', $google_api_database, 'delete_google_api_rezension');
        $this->loader->add_action($this->plugin_name . '/update_all_rezensionen', $google_api_database, 'update_all_rezensionen', 10, 3);
        $this->loader->add_action($this->plugin_name . '/update_rezension_optionen', $google_api_database, 'update_rezension_optionen');
        //
    }

    /**
     * Register all of the hooks for the Helper to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function google_api_helper()
    {

        global $api_helper;
        $api_helper = new Google_Rezensionen_Api_Helper($this->get_plugin_name(), $this->main);
        $this->loader->add_filter($this->plugin_name . '/array_to_object', $api_helper, 'goggleApiArrayToObject');
        $this->loader->add_filter($this->plugin_name . '/random_string', $api_helper, 'google_api_random_string');
        $this->loader->add_filter($this->plugin_name . '/generate_random_id', $api_helper, 'getGoogleApi2GenerateRandomId', 10, 4);
        $this->loader->add_filter($this->plugin_name . '/file_size_convert', $api_helper, 'googleApi2FileSizeConvert');
        $this->loader->add_filter($this->plugin_name . '/re_array_object', $api_helper, 'google_api_re_array_object');
        $this->loader->add_filter($this->plugin_name . '/destroy_dir', $api_helper, 'googleApiDestroyDir');
        $this->loader->add_filter($this->plugin_name . '/recursive_copy', $api_helper, 'google_api_recursive_copy', 10, 2);
        $this->loader->add_filter($this->plugin_name . '/move_file', $api_helper, 'google_api_move_file', 10, 3);
        $this->loader->add_filter($this->plugin_name . '/base64_decode_encode', $api_helper, 'google_api_base64_decode_encode', 10, 2);
        $this->loader->add_filter($this->plugin_name . '/get_menu_svg_icon', $api_helper, 'google_api_get_menu_svg_icon', 10, 2);
        $this->loader->add_filter($this->plugin_name . '/google_api_selects', $api_helper, 'google_api_selects');
        $this->loader->add_action($this->plugin_name . '/get_countries_select', $api_helper, 'get_hupa_countries_select');
        $this->loader->add_action($this->plugin_name . '/api_types', $api_helper, 'google_api_types');
        $this->loader->add_filter($this->plugin_name . '/generate_api_config_key', $api_helper, 'generate_api_config_key');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_google_api_curl_handle()
    {

        global $google_api_handle;
        $google_api_handle = new Google_Rezensionen_Api_Curl_Handle($this->get_plugin_name(), $this->get_version(), $this->main);

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_google_api_shortcodes()
    {

        global $google_api_shortcodes;
        $google_api_shortcodes = Google_Rezensionen_Shortcode::instance($this->get_plugin_name(), $this->get_version(), $this->main, $this->twig);

    }

    /**
     * Register all the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_google_api_classic_widget()
    {

        // global $google_api_widget;
        // $google_api_widget = new Google_Rezension_Api_Widget();
    }

    /**
     * Register API EDITOR Rest-Api Endpoints
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_api_editor_rest_api_routes()
    {
        $google_api_plugin_endpoint = new Google_Rezensionen_Api_Rest_Endpoint($this->get_plugin_name(), $this->get_version(), $this->main);
        $this->loader->add_action('rest_api_init', $google_api_plugin_endpoint, 'register_routes');
    }

    /**
     * Register API SRV Rest-Api Endpoints
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_srv_rest_api_routes()
    {
        $srv_rest_api = new Srv_Api_Enpoint($this->get_plugin_name(), $this->get_version(), $this->main);
        $this->loader->add_action('rest_api_init', $srv_rest_api, 'register_routes');
    }

    /**
     * Register API Rest-Api Callbacks
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_rezension_gutenberg_callback()
    {
        $google_api_plugin_gutenberg_callback = new Google_Rezensionen_Api_Block_Callback($this->get_plugin_name(), $this->get_version(), $this->main);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Google_Rezensionen_Api_Admin($this->get_plugin_name(), $this->get_version(), $this->main, $this->twig);

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        //TODO REGISTER Admin-Menu
        $this->loader->add_action('admin_menu', $plugin_admin, 'register_hupa_api_rezensionen_admin_menu');
        // Gutenberg Callback
        $this->loader->add_action('init', $plugin_admin, 'gutenberg_block_google_rezensionen_api_register');
        // Gutenberg Scripts
        $this->loader->add_action('enqueue_block_editor_assets', $plugin_admin, 'google_rezensionen_api_gutenberg_scripts');
        // Classic Widget
        //$this->loader->add_action('widgets_init', $plugin_admin, 'register_google_rezension_classic_widget');


        /** Register Plugin Settings Menu
         * @since    1.0.0
         */
        $this->loader->add_filter('plugin_action_links_' . $this->plugin_slug, $plugin_admin, 'google_rezensionen_api_plugin_add_action_link');

        // TODO AJAX ADMIN RESPONSE HANDLE
        $this->loader->add_action('wp_ajax_HupaGoogleApiHandle', $plugin_admin, 'prefix_ajax_HupaGoogleApiHandle');
        // TODO AJAX LICENSE API RESPONSE HANDLE
        $this->loader->add_action('wp_ajax_HupaLicenseAPIHandle', $plugin_admin, 'prefix_ajax_HupaLicenseAPIHandle');
        //
        //JOB EXTENSION CHECK
        $this->loader->add_action('plugin_loaded', $plugin_admin, 'check_install_extension');
        $this->loader->add_action('wp_ajax_GRExtensionAPIHandle', $plugin_admin, 'prefix_ajax_GRExtensionAPIHandle');
        //$this->loader->add_action('admin_init', $plugin_admin, 'check_srv_api_config');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Google_Rezensionen_Api_Public($this->get_plugin_name(), $this->get_version(), $this->main);


        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_ajax_wp_ajax_nopriv_HupaGooglePublicApiHandle', $plugin_public, 'prefix_ajax_HupaGooglePublicApiHandle');
        $this->loader->add_action('wp_ajax_HupaGooglePublicApiHandle', $plugin_public, 'prefix_ajax_HupaGooglePublicApiHandle');

    }

    /**
     * Register all public api hooks
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function wwdh_public_api()
    {

        global $googleRezensionPublicApi;
        $googleRezensionPublicApi = new Wwdh_Public_Api($this->get_version(), $this->get_plugin_name(), $this->main);

        /**
         * Get Public Support API Token
         * Input: support_id | support_secret
         * @since    1.0.0
         * return access_Token Object
         */
        $this->loader->add_filter('get_public_support_api_token', $googleRezensionPublicApi, 'get_wwdh_public_support_api_token', 10, 2);

        /**
         * Get Public API Resource
         * return API POST SUPPORT Resource Object
         * @since    1.0.0
         */
        $this->loader->add_filter('wwdh_support_api_resource', $googleRezensionPublicApi, 'wwdh_support_api_post_resource', 10, 3);

        /**
         * Get Public API Resource
         * return API POST JWT Resource Object
         * @since    1.0.0
         */

        //WARNING DELETE ?
        $this->loader->add_filter('get_public_resource_method', $googleRezensionPublicApi, 'wwdh_get_public_resource_method', 10, 3);


        // TODO API DOWNLOAD WARNING DELETE ?
        $this->loader->add_filter($this->plugin_name . '/wwdh_api_download', $googleRezensionPublicApi, 'wwdh_api_public_download', 10, 3);
        /**
         * Get Public API Ajax Resource Formular
         * @since    1.0.0
         */
        $this->loader->add_filter('get_public_api_select_commands', $googleRezensionPublicApi, 'wwdh_public_api_select_commands', 10, 3);

        /**
         * Get Public API Formular Language
         * @since    1.0.0
         */
        $this->loader->add_filter($this->plugin_name . '/get_ajax_language', $googleRezensionPublicApi, 'wwdh_ajax_language');

    }

    /**
     * Register all the DATABASE hooks
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function wwdh_extension_database()
    {
        global $experienceReportsDatabase;
        $experienceReportsDatabase = new Create_Wwdh_Extension_Database($this->get_db_version(), $this->plugin_name);
        /**
         * Create Database
         * @since    1.0.0
         */
        $this->loader->add_action('init', $experienceReportsDatabase, 'update_create_wwdh_extension_database');
    }

    /**
     * Register Class Wp_Experience_Reports Database
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function experience_reports_extension_database()
    {
        global $googleRezensionPublicApi;
        $googleRezensionPublicApi = new Wwdh_Public_Api($this->get_version(), $this->get_plugin_name(), $this->main);


        global $experienceReportsExtensionDB;
        $experienceReportsExtensionDB = new WWDH_Extension_Table($this->get_plugin_name(), $this->main);

        $this->loader->add_filter($this->plugin_name . '_get_extension', $experienceReportsExtensionDB, 'wwdh_get_extension', 10, 2);
        $this->loader->add_action($this->plugin_name . '_set_extension', $experienceReportsExtensionDB, 'wwdhSetExtension');
        $this->loader->add_action($this->plugin_name . '_update_extension_error', $experienceReportsExtensionDB, 'wwdhUpdateExtensionError');
        $this->loader->add_action($this->plugin_name . '_update_extension', $experienceReportsExtensionDB, 'wwdhUpdateExtension');
        $this->loader->add_action($this->plugin_name . '_delete_extension', $experienceReportsExtensionDB, 'wwdhDeleteExtension');
        $this->loader->add_action($this->plugin_name . '_update_extension_last_connect', $experienceReportsExtensionDB, 'wwdhUpdateExtensionLastConnect');
        $this->loader->add_action($this->plugin_name . '_update_extension_id_rsa', $experienceReportsExtensionDB, 'wwdhUpdateExtensionIdRsa');
        $this->loader->add_action($this->plugin_name . '_update_activated_extension', $experienceReportsExtensionDB, 'wwdhUpdateActivatedExtension');

    }

    /**
     * Register all the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_extension_helper_class()
    {
        global $plugin_extension_helper;
        $plugin_extension_helper = new Wwdh_Extension_Helper($this->get_version(), $this->plugin_name, $this->main);
        $this->loader->add_action($this->plugin_name . '/FileSizeConvert', $plugin_extension_helper, 'FileSizeConvert');
        //Download Extension Previews
        $this->loader->add_action($this->plugin_name . '/download_extension_previews', $plugin_extension_helper, 'download_extension_previews');
        //check is Table
        $this->loader->add_filter($this->plugin_name . '/check_extension_table', $plugin_extension_helper, 'check_report_extension_database_table');
        $this->loader->add_filter($this->plugin_name . '/get_plugin_folder', $plugin_extension_helper, 'get_plugin_folder');
        $this->loader->add_filter($this->plugin_name . '/get_directoryL_list', $plugin_extension_helper, 'getDirectoryList');

    }

    /**
     * Basic settings Extension Options for Wp_Experience_Reports
     *
     * Uses the BS_Formular2_Extensions class to register the extension options and hook.
     * register with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */

    private function google_rezensionen_extension_options()
    {

        global $extensionOptions;
        $extensionOptions = new GoogleRezensionExtensions($this->get_plugin_name(), $this->get_version(), $this->main, $this->twig);

        // TODO API-LOG
        $this->loader->add_action($this->plugin_name . '/set_api_log', $extensionOptions, 'wwdh_set_api_log', 10, 2);
        // TODO CHECK EXTENSION PREVIEW
        $this->loader->add_action($this->plugin_name . '/check_extension_preview_updates', $extensionOptions, 'wwdh_check_extension_preview_updates');
        // TODO Load Preview Extensions Data
        $this->loader->add_filter($this->plugin_name . '/get_extension_preview_url_data', $extensionOptions, 'wwdh_get_extension_preview_url_data');

        // TODO Twig HTML Template Loader
        $this->loader->add_action('twig_template_loader', $extensionOptions, 'wwdh_twig_template_loader');

        // TODO Return Description Template
        $this->loader->add_filter('get_extension_description_template', $extensionOptions, 'get_wwdh_extension_description_template', 10, 2);
        //TODO JOB CHECK LIZENZ
        $this->loader->add_filter($this->plugin_name . '/check_extensions_installs', $extensionOptions, 'wwdh_check_extensions_installs', 10, 2);

        $this->loader->add_action('check_delete_update_api_extension', $extensionOptions, 'wwdh_check_api_extension');

        // TODO Extension Language URLS
        $this->loader->add_filter('get_preview_language_url', $extensionOptions, 'wwdh_get_extension_preview_language_url');
        // TODO GET Extension Folder

        // TODO Activate Extension
        $this->loader->add_filter($this->plugin_name . '/extension_activate', $extensionOptions, 'wwdh_activate_extension', 10, 3);

        $this->loader->add_filter($this->plugin_name . '/read_folder', $extensionOptions, 'read_wwdh_folder', 10, 2);
    }

    /**
     * Register all extension api hooks
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function wwdh_extension_api()
    {
        global $wwdhExtensionApi;
        $wwdhExtensionApi = new WWDH_Extension_API($this->get_version(), $this->get_plugin_name(), $this->main);

        // TODO Get License Data
        $this->loader->add_filter($this->plugin_name . '/get_api_post_resource', $wwdhExtensionApi, 'wwdh_get_api_post_resource', 10, 4);
        $this->loader->add_filter($this->plugin_name . '/extension_download', $wwdhExtensionApi, 'wwdh_api_extension_download', 10, 3);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name(): string
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Google_Rezensionen_Api_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader(): Google_Rezensionen_Api_Loader
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version(): string
    {
        return $this->version;
    }

    /**
     * The API DIR
     *
     *
     * @return    string    API CURL DIR of the plugin.
     * @since     1.0.0
     */
    public function get_api_curl_dir(): string
    {
        return $this->api_curl_dir;
    }

    /**
     * Settings ID for Plugin.
     * @return int
     * @since    1.0.0
     */
    public function get_settings_id(): int
    {
        return $this->settings_id;
    }

    /**
     * Retrieve the database version number of the plugin.
     *
     * @return    string    The database version number of the plugin.
     * @since     1.0.0
     */
    public function get_db_version(): string
    {
        return $this->db_version;
    }

    /**
     * License Config for the plugin.
     *
     * @return    object License Config.
     * @since     1.0.0
     */
    public function get_license_config(): object
    {
        $config_file = plugin_dir_path(dirname(__FILE__)) . 'includes/license/config.json';
        return json_decode(file_get_contents($config_file));
    }

    /**
     * The EXTENSION PREVIEW DIR
     *
     *
     * @return    string     EXTENSION PREVIEW DIR of the plugin.
     * @since     1.0.0
     */
    public function get_extension_preview(): string
    {
        return $this->extension_preview;
    }

    /**
     * The API DIR
     *
     *
     * @return    string    API DIR of the plugin.
     * @since     1.0.0
     */
    public function get_api_dir(): string
    {
        return $this->api_dir;
    }

    /**
     * The Public Certificate
     *
     * @return    string    Public Certificate in BASE64.
     * @since     1.0.0
     */
    public function get_id_rsa(): string
    {
        return $this->id_rsa;
    }

    /**
     * The Public Certificate
     *
     * @return    string    Public Certificate in BASE64.
     * @since     1.0.0
     */
    public function get_plugin_rsa(): string
    {
        return $this->id_plugin_rsa;
    }

    public function get_twig_template_dir(): string
    {
        return $this->twig_template_dir;
    }

    public function get_srv_api_dir():string {
        return  $this->srv_api_dir;
    }

    public function get_plugin_api_config():object {
        return $this->plugin_api_config;
    }

}
