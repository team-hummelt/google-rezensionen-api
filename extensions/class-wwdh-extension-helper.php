<?php

namespace Goggle\Rezension;


use Exception;
use FilesystemIterator;
use stdClass;
use Google_Rezensionen_Api;

/**
 * The ADMIN Public API RESPONSE plugin class.
 *
 * @since      1.0.0
 * @package    Bs_Formular2
 * @subpackage Bs_Formular2/extensions
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Wwdh_Extension_Helper
{

    /**
     * TRAIT of Default Settings.
     *
     * @since    1.0.0
     */
    use Trait_Extension_Defaults;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $basename The ID of this plugin.
     */
    private string $basename;

    /**
     * Store plugin main class to allow public access.
     *
     * @since    1.0.0
     * @access   private
     * @var Google_Rezensionen_Api $main The main class.
     */
    private Google_Rezensionen_Api $main;

    /**
     * The Version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current Version of this plugin.
     */
    private string $version;

    /**
     * The Default Settings.
     *
     * @since    1.0.0
     * @access   private
     * @var      array|object $default The current version of the database Version.
     */
    private $default;


    /**
     * @param string $version
     * @param string $basename
     * @param Google_Rezensionen_Api $main
     */
    public function __construct(string $version, string $basename, Google_Rezensionen_Api $main)
    {

        $this->version = $version;
        $this->basename = $basename;
        $this->main = $main;
        $this->default = $this->get_theme_default_settings('');

    }

    /**
     * @param $array
     * @return object
     */
    public function ERArrayToObject($array): object
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::ERArrayToObject($value);
            }
        }

        return (object)$array;
    }


    public function object2array_recursive($object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * @param float $bytes
     *
     * @return string
     * @access final public
     */
    final public function FileSizeConvert( float $bytes ): string {
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
     * @throws Exception
     */
    private function recursive_copy($src, $dst)
    {

        $dir = opendir($src);
        if (!is_dir($dst)) {
            if (!mkdir($dst)) {
                throw new Exception('Destination Ordner nicht gefunden gefunden.');
            }
        }
        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    $this->recursive_copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                } else {
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }


    public function get_plugin_folder($directory, $search = ''): array
    {
        $scanned = array_diff(scandir($directory), array('..', '.'));
        $folderArr = [];
        foreach ($scanned as $tmp) {
            if (is_dir($directory . $tmp)) {
                if ($search) {
                    if ($search == $tmp) {
                        return $tmp;
                    }
                }
                $folderArr[] = $tmp;
            }
        }
        return $folderArr;
    }

    /**
     * @param $dir
     * @return bool
     */
    public function wwdhDestroyDir($dir): bool
    {
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);

        foreach (scandir($dir) as $file) {
            if ($file == "." || $file == "..")
                continue;
            if (!$this->wwdhDestroyDir($dir . "/" . $file)) {
                chmod($dir . "/" . $file, 0777);
                if (!$this->wwdhDestroyDir($dir . "/" . $file)) return false;
            }
        }
        return rmdir($dir);
    }

    public function download_extension_previews()
    {
        require_once ( ABSPATH . '/wp-admin/includes/file.php' );
        $url = get_option($this->basename . '-api-options')['public_api_resource_url'];
       // $api = apply_filters('get_public_resource_method', 'get_extensions', $url);
        global $googleRezensionPublicApi;
        $api = $googleRezensionPublicApi->wwdh_get_public_resource_method('get_extensions', $url);

        if (isset($api->status) && $api->status) {
            if (isset($api->data) && !empty($api->data)) {
                foreach ($api->data as $tmp) {
                    $dir = GOOGLE_REZENSION_EXTENSION_PREVIEW_DIR . $tmp->extension_filename . DIRECTORY_SEPARATOR;
                    if (is_dir($dir)) {
                        continue;
                    }
                    if (mkdir($dir, 0777, false)) {
                        $download = apply_filters($this->basename . '/wwdh_api_download', $tmp->download_url);
                        @file_put_contents($dir . $tmp->extension_filename . '.zip', $download);
                        WP_Filesystem();
                        $unZipFile = unzip_file($dir . $tmp->extension_filename . '.zip', GOOGLE_REZENSION_EXTENSION_PREVIEW_DIR);
                        if (!$unZipFile) {
                            do_action($this->basename.'/set_api_log', 'error', 'WP_Filesystem - unzip_file error');
                        } else {
                           @unlink($dir . $tmp->extension_filename . '.zip');
                        }
                    }
                }
            }
        }
    }

    /**
     * Load the plugin Wp_Experience_Reports Default Options.
     *
     * @since    1.0.0
     */
    public function wwdh_set_default_options()
    {
        // JOB API Options
        $apiDef = $this->ERArrayToObject($this->default['api_settings']);
        $apiOptions = get_option($this->basename . '-api-options');
        $apiDefaults = [
            'api_url' => $apiDef->api_url,
            'public_api_token_url' => $apiDef->public_api_token_url,
            'public_api_support_url' => $apiDef->public_api_support_url,
            'public_api_resource_url' => $apiDef->public_api_resource_url,
            'public_api_preview_url' => $apiDef->public_api_preview_url,
            //Extension
            'extension_api_activate_url' => $apiDef->extension_api_activate_url,
            // Token URL
            'extension_api_id_rsa_token' => $apiDef->extension_api_id_rsa_token,
            //Resource
            'extension_api_resource_url' => $apiDef->extension_api_resource_url,
            //Download
            'extension_api_extension_download' => $apiDef->extension_api_extension_download
        ];
        $apiOptions = wp_parse_args($apiOptions, $apiDefaults);
        update_option($this->basename . '-api-options', $apiOptions);

        if (get_option($this->basename . '/wwdh_extension_check')) {
            $ref = current_time('timestamp') - get_option($this->basename . '/wwdh_extension_check');
            if ($ref >= GOOGLE_REZENSION_UPDATE_EXTENSION_TIME) {
                do_action($this->basename . '/check_extension_preview_updates');
                update_option($this->basename . '/wwdh_extension_check', current_time('timestamp'));
            }
        }
    }

    public function getDirectoryList($dir):array {
        $dirList = $fileList = array();
        $iter = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);

        foreach($iter as $file) {
            if($file->isDir()) {
                $dirList[$file->getFilename()] = $this->getDirectoryList($file->getPathname());
            } else {
                $fileList[$file->getFilename()] = $file->getFilename();
            }
        }
        uksort($dirList, "strnatcmp");
        natsort($fileList);
        return $dirList + $fileList;
    }

}