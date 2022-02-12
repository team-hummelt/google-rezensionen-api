<?php

namespace Rezensionen\Widget;

/**
 * Define the Google Rezension CLASSIC Widget functionality.
 *
 * Loads and defines the API files for this plugin
 * so that it is ready for Rezensionen.
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 */

/**
 * Define the Google Rezension CLASSIC Widget functionality.
 *
 * Loads and defines the API files for this plugin
 * so that it is ready for Rezensionen.
 *
 * @since      1.0.0
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/includes
 * @author     Jens Wiecker <wiecker@hummelt.com>
 */


use Hupa\RezensionenApi\Google_Rezensionen_Api_Defaults_Trait;
use WP_Widget;

defined('ABSPATH') or die();


class Google_Rezension_Api_Widget extends WP_Widget
{
    /**
     * TRAIT of Default Settings.
     *
     * @since    1.0.0
     */
    use Google_Rezensionen_Api_Defaults_Trait;

    /**
     * Constructs the new widget.
     *
     * @see WP_Widget::__construct()
     *
     */
    function __construct()
    {
        // Instantiate the parent object.
        parent::__construct(false, __('Google Rezension', 'google-rezensionen-api'));
    }

    /**
     * The widget's HTML output.
     *
     * @param array $args Display arguments including before_title, after_title,
     *                        before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget.
     *
     * @see WP_Widget::widget()
     *
     */

    function widget($args, $instance)
    {
        $args     = (object) $args;
        $instance = (object) $instance;
        $header   = empty( $instance->header ) ? ' ' : apply_filters( 'widget_title', $instance->header );
        $selectTemplate = empty( $instance->selectTemplate ) ? '' : $instance->selectTemplate;
        $selectRezension  = empty( $instance->selectRezension ) ? '' : $instance->selectRezension;
        $bgColor = empty( $instance->bgColor ) ? '' : $instance->bgColor;
        echo( $args->before_widget ?? '' );
        echo $args->before_title . $header . $args->after_title;
        echo do_shortcode( '[google_rezension id="' . $selectRezension . '" bg="' . $bgColor . '" template="'.$selectTemplate.'" class=""]');
        echo( $args->after_widget ?? '' );
    }

    /**
     * The widget update handler.
     *
     * @param array $new_instance The new instance of the widget.
     * @param array $old_instance The old instance of the widget.
     *
     * @return array The updated instance of the widget.
     * @see WP_Widget::update()
     *
     */
    function update($new_instance, $old_instance): array
    {
        $instance             = $old_instance;
        $instance['header']   = $new_instance['header'];
        $instance['selectTemplate'] = $new_instance['selectTemplate'];
        $instance['selectRezension']  = $new_instance['selectRezension'];
        $instance['bgColor']     = $new_instance['bgColor'];
        return $instance;
    }

    /**
     * Output the admin widget options form HTML.
     *
     * @param array $instance The current widget settings.
     *
     * @return void The HTML markup for the form.
     */
    function form($instance): void
    {
        $instance = wp_parse_args((array)$instance, array(
            'title' => '',
        ));

        isset($instance['header']) && !empty($instance['header']) ? $header = filter_var($instance['header'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) : $header = __('Google Rezension', 'google-rezensionen-api');
        isset($instance['selectTemplate']) && !empty($instance['selectTemplate']) ? (int)$selectTemplate = filter_var($instance['selectTemplate'], FILTER_SANITIZE_NUMBER_INT) : $selectTemplate = 2;
        isset($instance['selectRezension']) && !empty($instance['selectRezension']) ? $selectRezension = filter_var($instance['selectRezension'], FILTER_SANITIZE_STRING) : $selectRezension = '';
        isset($instance['bgColor']) && !empty($instance['bgColor']) ? $bgColor = filter_var($instance['bgColor'], FILTER_SANITIZE_STRING) : $bgColor = '';
        ?>
        <p>
        <label for="<?= $this->get_field_id('header'); ?>"><?= __('Title', 'google-rezensionen-api') ?> </label>
            <input class="widefat" id="<?= $this->get_field_id('header'); ?>"
                   name="<?= $this->get_field_name('header'); ?>" type="text"
                   value="<?= esc_attr($header); ?>"/>
        </p>
        <label for="<?= $this->get_field_id('selectRezension'); ?>">
            <?= __('Rezension select', 'google-rezensionen-api') ?>  </label>
        <select class="widefat" id="<?= esc_attr($this->get_field_id('selectRezension')); ?>"
                name="<?= ($this->get_field_name('selectRezension')) ?>">
            <option value=""><?= __('select', 'google-rezensionen-api') ?>...</option>
            <?php
            $rezensionenData = apply_filters(GOOGLE_REZENSIONEN_API_BASENAME . '/get_api_rezension', '' );
            if($rezensionenData->status): foreach ($rezensionenData->record as $tmp):
            ?>
             <option value="<?= $tmp->place_id ?>" <?= esc_attr(selected(true, $selectRezension == $tmp->place_id)) ?>><?= $tmp->name ?></option>
            <?php  endforeach; endif; ?>
        </select>
        <p>
        <label for="<?= $this->get_field_id('selectTemplate'); ?>">
            <?= __('Template select', 'google-rezensionen-api') ?>  </label>
        <select class="widefat" id="<?= esc_attr($this->get_field_id('selectTemplate')); ?>"
                name="<?= ($this->get_field_name('selectTemplate')) ?>">
            <option value=""><?= __('select', 'google-rezensionen-api') ?>...</option>
            <?php
            $templates = apply_filters(GOOGLE_REZENSIONEN_API_BASENAME . '/google_api_selects', 'ausgabe_template_select');
            foreach ($templates as $key => $val) : ?>
                <option value="<?= $key ?>" <?= esc_attr(selected(true, $selectTemplate == $key)) ?>><?= $val ?></option>
            <?php endforeach; ?>
        </select>
        </p>
        <p>
            <label style="display: block" class="widefat" for="<?= $this->get_field_id('bgColor'); ?>"><?= __('Background color', 'google-rezensionen-api') ?> </label>
            <input class="widefat google-api-widget-color" id="<?= $this->get_field_id('bgColor'); ?>"
                   name="<?= $this->get_field_name('bgColor'); ?>" type="text"
                   value="<?= esc_attr($bgColor); ?>"/>
        </p>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.google-api-widget-color').wpColorPicker();
            });
        </script>
        <?php
    }
}
