<?php

namespace Rezensionen\AdminRegister;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/admin
 */

use Google_Rezensionen_Api;
use GoogleRezension\Extensions\WWDH_Api_Ajax;
use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use Puc_v4_Factory;
use Rezensionen\AdminAjax\Google_Rezensionen_Api_Admin_Ajax;
use Rezensionen\Endpoints\Google_Rezensionen_Api_Block_Callback;
use Rezensionen\LicenseAjax\Hupa_Api_License_Ajax;

//use Rezensionen\Widget\Google_Rezension_Api_Widget;
use Rezensionen\SrvApi\Api_Request_Exec;
use Twig\Environment;



/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/admin
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */
class Google_Rezensionen_Api_Admin
{

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
    public function __construct(string $plugin_name, string $version, Google_Rezensionen_Api $main, Environment $twig)
    {

        $this->basename = $plugin_name;
        $this->version = $version;
        $this->main = $main;
        $this->twig = $twig;

    }

    /**
     * Register the Plugin Settings Link.
     *
     * @since    1.0.0
     */
    public static function google_rezensionen_api_plugin_add_action_link($data)
    {
        // check permission
        if (!current_user_can('manage_options')) {
            return $data;
        }

        return array_merge(
            $data,
            array(
                sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg(
                        array(
                            'page' => 'google-api-rezensionen-options'
                        ),
                        admin_url('options-general.php')
                    ),
                    __("Settings", "google-rezensionen-api")
                )
            )
        );
    }

    /**
     * Register Register Admin Menu
     *
     * @since    1.0.0
     */

    public function register_hupa_api_rezensionen_admin_menu(): void
    {
        $manageOption = apply_filters($this->basename . '/get_settings', 'user_capability');
        $manageOption->status ? $manage_options = $manageOption->user_capability : $manage_options = 'manage_options';
        add_menu_page(
            __('Rezensionen', 'google-rezensionen-api'),
            __('Rezensionen', 'google-rezensionen-api'),
            $manage_options,
            'google-rezensionen-api',
            '',
            self::get_svg_icons('google'), 102
        );

        $hook_suffix = add_submenu_page(
            'google-rezensionen-api',
            __('Overview', 'google-rezensionen-api'),
            __('Overview', 'google-rezensionen-api'),
            $manage_options,
            'google-rezensionen-api',
            array($this, 'api_rezensionen_overview_page'));

        add_action('load-' . $hook_suffix, array($this, 'api_rezensionen_load_ajax_admin_options_script'));

        /*$hook_suffix = add_submenu_page(
            'google-rezensionen-api',
            __( 'Help', 'google-rezensionen-api' ),
            __( 'Help', 'google-rezensionen-api' ),
            $manage_options,
            'google-rezensionen-help',
            array( $this, 'api_rezensionen_help_page' ) );

        add_action( 'load-' . $hook_suffix, array( $this, 'api_rezensionen_load_ajax_admin_options_script' ) );*/

        $hook_suffix = add_submenu_page(
            'google-rezensionen-api',
            __('Extensions', 'google-rezensionen-api'),
            '<span style="color: greenyellow">➤&nbsp; ' . __('Extensions', 'google-rezensionen-api') . '</span>',
            'manage_options',
            'google-rezensionen-api-extension',
            array($this, 'google_rezensionen_api_extension_page'));

        add_action('load-' . $hook_suffix, array($this, 'api_rezensionen_load_ajax_admin_options_script'));

        // TODO OPTIONS PAGE
        $hook_suffix = add_options_page(
            __('Rezensionen', 'google-rezensionen-api'),
            '<img class="menu_gapi" src="' . apply_filters($this->basename . '/get_menu_svg_icon', 'google') . '">' . __('Rezensionen', 'google-rezensionen-api'),
            $manage_options,
            'google-api-rezensionen-options',
            array($this, 'hupa_google_api_rezensionen_options_page')
        );

        add_action('load-' . $hook_suffix, array($this, 'api_rezensionen_load_ajax_admin_options_script'));
    }

    /**
     * Register Google Rezensionen Api ADMIN PAGE
     *
     * @since    1.0.0
     */
    public function api_rezensionen_overview_page(): void
    {
        require_once 'partials/google-rezensionen-api-admin-startpage.php';
    }

    /**
     * Register GOOGLE REZENSIONEN API ADMIN PAGE
     *
     * @since    1.0.0
     */
    public function api_rezensionen_help_page(): void
    {
        require_once 'partials/google-rezensionen-api-admin-page-help.php';
    }

    /**
     * Register GOOGLE REZENSIONEN EXTENSION ADMIN PAGE
     *
     * @since    1.0.0
     */
    public function google_rezensionen_api_extension_page(): void
    {
        require_once 'partials/google-rezensionen-api-admin-extension.php';
    }

    /**
     * Register GOOGLE REZENSIONEN API OPTION PAGE
     *
     * @since    1.0.0
     */
    public function hupa_google_api_rezensionen_options_page()
    {
        require_once 'partials/google-rezensionen-api-admin-options-page.php';
    }

    public function api_rezensionen_load_ajax_admin_options_script(): void
    {
        add_action('admin_enqueue_scripts', array($this, 'load_google_rezensionen_admin_scripts'));
        $title_nonce = wp_create_nonce('google_rezensionen_api_admin_handle');

        wp_register_script('google-rezensionen-script', '', [], '', true);
        wp_enqueue_script('google-rezensionen-script');
        wp_localize_script('google-rezensionen-script', 'rezensionen_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $title_nonce,
            'extension_preview_url' => GOGGLE_REZENSION_EXTENSION_PREVIEW_URL,
            'ext_lang' => apply_filters('get_preview_language_url', '')
        ));
    }


    /**
     * Register GOOGLE REZENSIONEN Classic Widget
     *
     * @since    1.0.0
     */
    public function register_google_rezension_classic_widget(): void
    {
        //register_widget( Google_Rezension_Api_Widget::class);
    }

    /**
     * Register GOOGLE REZENSIONEN API AJAX ADMIN RESPONSE HANDLE
     *
     * @since    1.0.0
     */
    public function prefix_ajax_HupaGoogleApiHandle(): void
    {
        check_ajax_referer('google_rezensionen_api_admin_handle');

        /**
         * The class for defining AJAX in the admin area.
         */
        require_once 'ajax/class_google_rezensionen_api_admin_ajax.php';
        $adminGoogleApiAjaxHandle = new Google_Rezensionen_Api_Admin_Ajax($this->basename, $this->main, $this->twig);
        wp_send_json($adminGoogleApiAjaxHandle->google_rezensionen_api_admin_ajax_handle());
    }

    /**
     * Register BS-Formular2 AJAX API RESPONSE HANDLE
     *
     * @since    1.0.0
     */
    public function prefix_ajax_GRExtensionAPIHandle()
    {

        check_ajax_referer('google_rezensionen_api_admin_handle');
        /**
         * The class for defining AJAX in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/ajax/class_wwdh_api_ajax.php';
        $adminAjaxHandle = new WWDH_Api_Ajax($this->version, $this->basename, $this->main);
        wp_send_json($adminAjaxHandle->wwdh_api_ajax_handle());
    }

    public function check_install_extension()
    {

        //apply_filters($this->basename.'/check_extensions_installs','');
        $time = get_option($this->basename . '/wwdh_extension_check') + GOOGLE_REZENSION_UPDATE_EXTENSION_TIME;
        if ($time < current_time('timestamp')) {
            apply_filters($this->basename . '/check_extensions_installs', '');
            update_option($this->basename . '/wwdh_extension_check', current_time('timestamp'));
        }


    }

    public function check_srv_api_config()
    {
        $config = $this->main->get_plugin_api_config();
        if (!$config->rest_url) {
            $config->rest_url = rest_url('plugin/' . $this->basename . '/v' . $this->version . '/');
            $config->site_url = site_url();
            $config->license = 0;
            $config->basename = $this->basename;
        }
    }

    /**
     * Register GOOGLE REZENSIONEN API REGISTER GUTENBERG BLOCK TYPE
     *
     * @since    1.0.0
     */
    public function gutenberg_block_google_rezensionen_api_register()
    {
        register_block_type('hupa/google-rezensionen-api', array(
            'render_callback' => [Google_Rezensionen_Api_Block_Callback::class, 'callback_google_rezensionen_api'],
            'editor_script' => 'google-rezensionen-api-gutenberg',
        ));

        //add_filter( 'gutenberg_google_rezension_api_render', 'gutenberg_block_google_rezension_api_render_filter', 10, 20 );
    }

    /**
     * Register GOOGLE REZENSIONEN API REGISTER GUTENBERG SCRIPTS
     *
     * @since    1.0.0
     */
    public function google_rezensionen_api_gutenberg_scripts(): void
    {

        $plugin_asset = require GOOGLE_REZENSION_API_GB_BUILD_DIR . 'index.asset.php';
        // Scripts
        wp_enqueue_script(
            'google-rezensionen-api-gutenberg',
            plugins_url($this->basename) . '/admin/Gutenberg/plugin-data/build/index.js',
            $plugin_asset['dependencies'], $plugin_asset['version']
        );

        // Style
        wp_enqueue_style(
            'google-rezensionen-api-gutenberg', // Handle.
            plugins_url($this->basename) . '/admin/Gutenberg/plugin-data/build/index.css', array(), $this->version
        );

        wp_register_script('google-rezensionen-api-gutenberg-js-localize', '', [], $this->version, true);
        wp_enqueue_script('google-rezensionen-api-gutenberg-js-localize');
        wp_localize_script('google-rezensionen-api-gutenberg-js-localize',
            'GoReApiRestObj',
            array(
                'url' => esc_url_raw(rest_url('google-rezensionen-endpoint/v1/')),
                'nonce' => wp_create_nonce('wp_rest')
            )
        );
    }

    /**
     * Register GOOGLE REZENSIONEN API AJAX API RESPONSE HANDLE
     *
     * @since    1.0.0
     */
    public function prefix_ajax_HupaLicenseAPIHandle()
    {

        check_ajax_referer('google_rezensionen_api_admin_handle');
        /**
         * The class for defining AJAX in the admin area.
         */
        require_once 'ajax/class_hupa_api_license_ajax.php';
        $adminAjaxHandle = new Hupa_Api_License_Ajax($this->basename, $this->main);
        wp_send_json($adminAjaxHandle->hupa_license_api_admin_ajax_handle());
    }

    /**
     * Register the Update-Checker for the Plugin.
     *
     * @since    1.0.0
     */
    public function set_google_rezensionen_update_checker()
    {

        if (get_option("{$this->basename}_update_config") && get_option($this->basename . '_update_config')->update->update_aktiv) {
            $postSelectorUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                get_option("{$this->basename}_update_config")->update->update_url_git,
                WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->basename . DIRECTORY_SEPARATOR . $this->basename . '.php',
                $this->basename
            );

            switch (get_option("{$this->basename}_update_config")->update->update_type) {
                case '1':
                    $postSelectorUpdateChecker->getVcsApi()->enableReleaseAssets();
                    break;
                case '2':
                    $postSelectorUpdateChecker->setBranch(get_option("{$this->basename}_update_config")->update->branch_name);
                    break;
            }
        }
    }

    /**
     * add plugin upgrade notification
     */

   public function google_rezensionen_api_show_upgrade_notification( $current_plugin_metadata, $new_plugin_metadata ) {

        if ( isset( $new_plugin_metadata->upgrade_notice ) && strlen( trim( $new_plugin_metadata->upgrade_notice ) ) > 0 ) {
            // Display "upgrade_notice".
            echo sprintf( '<span style="background-color:#d54e21;padding:10px;color:#f9f9f9;margin-top:10px;display:block;"><strong>%1$s: </strong>%2$s</span>', esc_attr('Important Upgrade Notice', 'google-rezensionen-api'), esc_html( rtrim( $new_plugin_metadata->upgrade_notice ) ) );

        }
    }



    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

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

        //wp_enqueue_style( $this->basename, plugin_dir_url( __FILE__ ) . 'css/google-rezensionen-api-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style($this->basename . '-tools', plugin_dir_url(__FILE__) . 'css/tools.css', array(), $this->version, false);
        wp_enqueue_style($this->basename . '-Glyphter', plugin_dir_url(__FILE__) . 'css/Glyphter.css', array(), $this->version, false);
    }

    /**
     * Register GOOGLE REZENSIONEN API ADMIN SCRIPTS
     *
     * @since    1.0.0
     */
    public function load_google_rezensionen_admin_scripts(): void
    {

        wp_enqueue_style($this->basename . '-bootstrap-icons', plugin_dir_url(__DIR__) . 'includes/tools/bootstrap/bootstrap-icons.css', array(), $this->version, 'all');
        wp_enqueue_style($this->basename . '-sweetalert2', plugin_dir_url(__DIR__) . 'includes/tools/sweetalert2/sweetalert2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->basename . '-animate', plugin_dir_url(__DIR__) . 'includes/tools/animate.min.css', array(), $this->version, 'all');
        //TODO FontAwesome / Bootstrap
        wp_enqueue_style($this->basename . '-admin-bs-style', plugins_url($this->basename) . '/admin/css/bs/bootstrap.min.css', array(), $this->version, false);
        // TODO ADMIN ICON
        wp_enqueue_style($this->basename . '-admin-icons-style', plugins_url($this->basename) . '/admin/css/font-awesome.css', array(), $this->version, false);
        // TODO DASHBOARD
        wp_enqueue_style($this->basename . '-admin-dashboard-style', plugins_url($this->basename) . '/admin/css/admin-dashboard-style.css', array(), $this->version, false);
        wp_enqueue_style($this->basename . '-data-table-style', plugins_url($this->basename) . '/admin/css/tools/dataTables.bootstrap5.min.css', array(), $this->version, false);

        wp_enqueue_script($this->basename . '-bs-bundle', plugins_url($this->basename) . '/admin/js/bs/bootstrap.bundle.min.js', array(), $this->version, true);
        wp_enqueue_script($this->basename . '-sweetalert2', plugin_dir_url(__DIR__) . 'includes/tools/sweetalert2/sweetalert2.all.min.js', array(), $this->version, true);
        wp_enqueue_script($this->basename . '-extension', plugin_dir_url(__FILE__) . 'js/google-rezension-extension.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->basename . '-jquery-table-js', plugins_url($this->basename) . '/admin/js/tools/data-table/jquery.dataTables.min.js', array(), $this->version, true);
        wp_enqueue_script($this->basename . '-bs5-data-table', plugins_url($this->basename) . '/admin/js/tools/data-table/dataTables.bootstrap5.min.js', array(), $this->version, true);
        wp_enqueue_script($this->basename . '-api', plugins_url($this->basename) . '/admin/js/license-api.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->basename, plugins_url($this->basename) . '/admin/js/google-rezensionen-api-admin.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->basename . '-table-bs', plugins_url($this->basename) . '/admin/js/formular-table.js', array('jquery'), $this->version, true);
    }

    /**
     * @param $name
     *
     * @return string
     */
    private static function get_svg_icons($name): string
    {
        $icon = '';
        switch ($name) {
            case'google':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-google" viewBox="0 0 16 16">
                         <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                         </svg>';
                break;

            default:
        }
        return 'data:image/svg+xml;base64,' . base64_encode($icon);

    }
}
