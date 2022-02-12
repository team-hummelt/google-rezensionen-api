<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.hummelt-werbeagentur.de/
 * @since      1.0.0
 *
 * @package    Google_Rezensionen_Api
 * @subpackage Google_Rezensionen_Api/admin/partials
 */

$dbData = apply_filters($this->basename . '/get_settings', 'app_settings');
$value = json_decode($dbData->app_settings);
$imgUrl = plugins_url($this->basename) . '/admin/images/';

?>
<div class="wp-bs-starter-wrapper">
    <div class="container">
        <div class="card shadow-sm">

            <h5 class="card-header d-flex align-items-center bg-google py-4">
                <i class="fa fa-google"></i> &nbsp;
                <?= __('Google Rezensionen', 'google-rezensionen-api') ?>
            </h5>

            <div class="card-body" style="min-height: 72vh">
                <div class="card shadow-sm mb-3">
                    <h5 class="card-header bg-white py-4">
                        <i class="fa fa-life-bouy wp-color"></i>
                        <?= __('Google Rezensionen', 'google-rezensionen-api') ?> <?= __('Help', 'google-rezensionen-api') ?>
                    </h5>
                    <div class="card-body" style="min-height: 60vh">
                        <div class="col-xl-8 col-lg-10 col-12 mx-auto">

                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button collapsed no-blur" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="false"
                                                aria-controls="collapseOne">
                                            <?= __('Templates shortcode and widget output', 'google-rezensionen-api') ?>

                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse"
                                         aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body bg-light">
                                            <div class="template-help-wrapper">
                                                <div class="help-item mb-3">
                                                    <strong>Hier ist eine Übersicht der Standard-Templates.</strong> In
                                                    der Version <b class="strong-font-weight">Google-Rezensionen-Pro</b>
                                                    gibt es zusätzlich Slider mit Kommentare und Filter Funktionen für
                                                    die Bewertung und Kommentar ausgabe.
                                                    <hr>
                                                        <h4 class="mb-3"> Verfügbare Standard-Templates</h4>
                                                    <img class="img-fluid" src="<?= $imgUrl ?>temp-xxl.png" alt="">
                                                    <div class="form-text mb-2">Ausgabe Template Shortcode ID:1
                                                        | Gutenberg Widget: XXL
                                                    </div>
                                                </div>
                                                    <hr>
                                                <div class="help-item mb-3">
                                                    <img class="img-fluid" src="<?= $imgUrl ?>temp-xl.png" alt="">
                                                    <div class="form-text mb-2">Ausgabe Template Shortcode ID:2
                                                        | Gutenberg Widget: XL
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="help-item mb-3">
                                                    <img class="img-fluid" src="<?= $imgUrl ?>temp-md.png" alt="">
                                                    <div class="form-text mb-2">Ausgabe Template Shortcode ID:3
                                                        | Gutenberg Widget: MD
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="help-item mb-3">
                                                    <img class="img-fluid" src="<?= $imgUrl ?>temp-sm.png" alt="">
                                                    <div class="form-text mb-2">Ausgabe Template Shortcode ID:4
                                                        | Gutenberg Widget: SM
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="help-item mb-3">
                                                    <img class="img-fluid" src="<?= $imgUrl ?>temp-xs.png" alt="">
                                                    <div class="form-text mb-2">Ausgabe Template Shortcode ID:5
                                                        | Gutenberg Widget: XS
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed no-blur" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                            Shortcode Optionen
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                         aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body bg-light">
                                            <strong>This is the second item's accordion body.</strong> It is hidden by
                                            default, until the collapse plugin adds the appropriate classes that we use
                                            to style each element. These classes control the overall appearance, as well
                                            as the showing and hiding via CSS transitions. You can modify any of this
                                            with custom CSS or overriding our default variables. It's also worth noting
                                            that just about any HTML can go within the <code>.accordion-body</code>,
                                            though the transition does limit overflow.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed no-blur" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                            Accordion Item #3
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                         aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body bg-light">
                                            <strong>This is the third item's accordion body.</strong> It is hidden by
                                            default, until the collapse plugin adds the appropriate classes that we use
                                            to style each element. These classes control the overall appearance, as well
                                            as the showing and hiding via CSS transitions. You can modify any of this
                                            with custom CSS or overriding our default variables. It's also worth noting
                                            that just about any HTML can go within the <code>.accordion-body</code>,
                                            though the transition does limit overflow.
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <small class="small d-inline-block text-end position-relative">
                <div class="position-absolute bottom-0 end-0">
                    <span class="d-inline-block pe-3 pb-1 text-small">
                        DB:  <b class="strong-font-weight text-danger d-inline-block pe-1">v<?= GOOGLE_REZENSIONEN_API_DB_VERSION ?></b>
                        Version: <b class="strong-font-weight text-danger">v<?= $this->version ?></b>
                    </span>
				</div>
			</small>
		</div>
	</div>
</div>
<div id="snackbar-success"></div>
<div id="snackbar-warning"></div>