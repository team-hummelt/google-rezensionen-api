<?php

namespace Hupa\RezensionenApi;

defined('ABSPATH') or die();

/**
 * ADMIN Settings TRAIT
 * @package Hummelt & Partner WordPress-Plugin
 * Copyright 2022, Jens Wiecker
 * License: Commercial - goto https://www.hummelt-werbeagentur.de/
 *
 * @Since 1.0.0
 */
trait Google_Rezensionen_Api_Defaults_Trait
{

    //DATABASE TABLES
	protected string $table_google_api_settings = 'api_rezensionen_settings';
	protected string $table_google_api_rezensionen = 'api_rezensionen';
	protected string $table_countries = 'api_countries';

    //SETTINGS DEFAULT OBJECT
    protected array $google_rezensionen_api_defaults;

	//DEFAULT SETTINGS
	protected string $user_capability = 'manage_options';
	protected string $google_api_url = 'https://maps.googleapis.com/maps/api/';
	protected int $google_ds_show = 1;
	protected int $completion_aktiv = 0;
	// Google Map Sync Settings
	protected string $google_map_type = 'roadmap';
	protected int $horizontal_size = 400;
	protected int $vertical_size = 400;
	protected int $scale2_aktiv = 1;
	protected string $map_image_format = 'png';
	protected int $static_card_zoom = 15;

	private int $update_interval = 2;
	private int $update_option = 1;

	/**
	 * @param string $args
	 * @param string $field
	 *
	 * @return array
	 */
    protected function get_theme_default_settings(string $args = '', string $field = ''): array
    {
        $this->google_rezensionen_api_defaults = [
			'default_settings' => [
				'user_capability' => $this->user_capability,
				'app_settings' => [
					'google_api_url' => $this->google_api_url,
					'google_api_key' => '',
					'google_ds_show' => $this->google_ds_show,
					'completion_aktiv' => $this->completion_aktiv
				]
			],
	        'api_sync_settings' => [
				'google_map_type' => $this->google_map_type,
		        'horizontal_size' => $this->horizontal_size,
		        'vertical_size' => $this->vertical_size,
		        'scale2_aktiv' => $this->scale2_aktiv,
		        'map_image_format' => $this->map_image_format,
		        'static_card_zoom' =>$this->static_card_zoom,
		        'update_option' => $this->update_option,
		        'update_interval' => $this->update_interval,
	        ],
	        'ajax_msg' => [
				'error_msg' => [
					//<b class="strong-font-weight text-danger">Ajax Fehler:</b> Es wurden keine Daten empfangen.
					'ajax_error' => __('<b class="strong-font-weight">Ajax error:</b> No data was received.', 'google-rezensionen-api'),
					//Das Feld <b>%s</b> darf nicht leer sein.
					'err_field' =>  sprintf(__('The field <i>%s</i> must not be empty.', 'google-rezensionen-api'), $field),
					//Um das Plugin nutzen zu können, muss darf das Feld <b>%s</b> nicht leer sein.
					'err_options_field' =>  sprintf(__('To be able to use the plugin, the field <b>%s</b> must not be empty.', 'google-rezensionen-api'), $field),
					'err_ds_check' => __('You must <b class="strong-font-weight">accept</b> the privacy policy', 'google-rezensionen-api'),
					//Das Eingabefeld darf <b class="strong-font-weight">nicht leer sein!</b>
					'search_empty' => __('The input field must <b class="strong-font-weight">not be empty!</b>', 'google-rezensionen-api'),
					'place_existing' =>  __('There is already <b class="strong-font-weight">an entry</b> with this ID!', 'google-rezensionen-api'),
					//Es gibt nichts zu aktualisieren
					'not_update' => __('There is nothing to update!','google-rezensionen-api')
				],
		        'templates' => [
					//Google Eintrag
					'google_entry' => __('Google entry', 'google-rezensionen-api'),
			        //Eintrag auswählen
					'select_entry' => __('Select entry', 'google-rezensionen-api'),
			        //kopiert
			        'copies' => __('copies', 'google-rezensionen-api'),
					'copy' => __('copy', 'google-rezensionen-api'),
			        //Place-ID
					'place_id' => __('Place-ID', 'google-rezensionen-api'),
			        //Google Rezensionen
					'google_rezensionen' => __('Google Rezensionen', 'google-rezensionen-api'),
			        //Rezensionen
					'rezensionen' => __('Rezensionen', 'google-rezensionen-api'),
			        //Rezension
					'rezension' => __('Rezension', 'google-rezensionen-api'),
			        'reviews' => __('Reviews', 'google-rezensionen-api'),
					'google' => __('Google', 'google-rezensionen-api'),
			        'address' => __('Address', 'google-rezensionen-api'),
			        'phone' => __('Phone', 'google-rezensionen-api'),
					'stand' => __('Stand', 'google-rezensionen-api'),
					'close' => __('close', 'google-rezensionen-api'),
			        //
			        'synchronization_settings' => __( 'API synchronization settings', 'google-rezensionen-api' ),
			        'updates'=> __( 'Updates', 'google-rezensionen-api' ),
			        'update_automatically' =>  __( 'Update automatically', 'google-rezensionen-api' ),
			        'update_manually' => __( 'Update manually', 'google-rezensionen-api' ),
			        'update_interval' => __( 'Update Interval', 'google-rezensionen-api' ),
			        'update_now' => __( 'Update now', 'google-rezensionen-api' ),
			        'synchronization_info' => __( 'The data from the Google Business API is not retrieved with every page view but is cached in a database. With the API synchronization settings, you can specify how often the data is updated.', 'google-rezensionen-api' ),
					'map_static_info' =>  __( 'The Maps Static API returns an image of the requested map. A Maps Static API image is embedded in the src attribute of a tag.', 'google-rezensionen-api' ),
					'individuell_settings' => __( 'Custom API synchronization settings', 'google-rezensionen-api' ),
			        //Rezension jetzt synchronisieren
					'synchronize_review_now' => __( 'Synchronize review now', 'google-rezensionen-api' ),
			        'head_download_settings' => __( 'Images and Maps Download Settings', 'google-rezensionen-api' ),
			        'shortcode' => __( 'Shortcode', 'google-rezensionen-api' ),
			        //Zusätzlich kann die Hintergrundfarbe und die Template-ID an den Shortcode übergeben werden.
					'shortcode_info' => __( 'Additionally, the background color and template ID can be passed to the shortcode.', 'google-rezensionen-api' ),
			        //Folgende Templates können ausgewählt werden:
					'shortcode_example_txt' => __( 'The following templates can be selected:', 'google-rezensionen-api' ),
			        //Shortcode mit Template-ID und geänderter Hintergrundfarbe
					'shortcode_out_txt' => __( 'Shortcode with template ID and changed background color.', 'google-rezensionen-api' ),
					'example' => __( 'Example', 'google-rezensionen-api' ),
			        'google_maps' => __( 'Google Maps', 'google-rezensionen-api' ),
			        'map_type' => __( 'Map type', 'google-rezensionen-api' ),
			        'static_map' => __( 'Static Map', 'google-rezensionen-api' ),
			        'card_size' => __( 'Card size', 'google-rezensionen-api' ),
			        'card' => __( 'Card', 'google-rezensionen-api' ),
			        'horizontal' => __( 'Horizontal', 'google-rezensionen-api' ),
			        'vertical' => __( 'Vertical', 'google-rezensionen-api' ),
			        'image_format' => __( 'Image format', 'google-rezensionen-api' ),
			        'zoom' => __( 'Zoom', 'google-rezensionen-api' ),
			        'create_high' => __( 'Create high resolution image', 'google-rezensionen-api' ),
			        'synchronize_now' => __('Synchronize now', 'google-rezensionen-api' ),
					'to_the_overview' => __('to the overview', 'google-rezensionen-api' ),
			        'no_api_key_found' => __( 'API-KEY not found', 'google-rezensionen-api' ),
					'no_key_info_one' => sprintf( __( 'To be able to create reviews, you need an  <b class="strong-font-weight">API KEY</b>. You can enter your API KEY under <a class="strong-font-weight" href="%s">Settings Reviews</a>.  You can find out how to create an API KEY <a target="_blank" class="fw-normal"
                                              href="https://developers.google.com/my-business/content/basic-setup">here</a>.', 'google-rezensionen-api' ), admin_url() . 'options-general.php?page=google-api-rezensionen-options' ),
					'no_key_info_second' => __( 'You will need the My Business API to output reviews.<br> The Google My Business API is an automated process that allows authorized individuals to manage location data for Google Maps.', 'google-rezensionen-api' ),
					'no_data_info' => __( 'With the Google Reviews API plugin you can easily view your reviews. Don\'t worry about privacy, the reviews are synchronized in the background. Data of your visitors will not be transferred to Google.', 'google-rezensionen-api' ),
			        ]
	        ]
        ];

        if ($args) {
            return $this->google_rezensionen_api_defaults[$args];
        } else {
            return $this->google_rezensionen_api_defaults;
        }
    }
}