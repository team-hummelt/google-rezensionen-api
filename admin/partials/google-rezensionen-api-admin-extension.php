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
?>
<div class="wp-bs-starter-wrapper">
    <div class="container">
        <div class="card  shadow-sm">
            <h5 class="card-header d-flex align-items-center bg-google py-4">
                <i class="fa fa-google"></i> &nbsp;
				<?= __( 'Google Rezensionen', 'google-rezensionen-api' ) ?>
            </h5>
            <div class="card-body" style="min-height: 72vh">
                <div class="card shadow-sm mb-3">
                    <h5 class="card-header py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="#1673aa" class="bi bi-subtract" viewBox="0 0 21 21"> <path d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2z"></path> </svg>
                        <!-- <?= __( 'Google Business API', 'google-rezensionen-api' ) ?> <?= __( 'Extensions', 'google-rezensionen-api' ) ?>
                        -->
	                    <?= __( 'There are currently no extensions available.', 'google-rezensionen-api' ) ?>
                    </h5>
                    <div class="card-body" style="min-height: 60vh">
                        <div class="col-xl-10 col-12 mx-auto">
                            <div class="px-4 pt-3 mt-3 pb-5 mb-5 text-center d-flex flex-column  position-relative">
                                <div><a target="_blank" href="https://www.hummelt-werbeagentur.de/">
                                        <i class="hupa-color icon-hupa-white hupa-scale-img fa-5x d-inline-block text-center py-2"></i>
                                    </a>
                                    <h1 class="" style="font-weight: 400;letter-spacing: -.5px;"><b class="text-google">G</b>oogle
	                                    <?= __( 'Reviews', 'google-rezensionen-api' ) ?>
                                    </h1>
                                    <div class="col-lg-8 mx-auto">
                                        <p class="lead  mb-4">
	                                        <?= __( 'With the Google Reviews API plugin you can easily view your reviews. Don\'t worry about privacy, the reviews are synchronized in the background. Data of your visitors will not be transferred to Google.', 'google-rezensionen-api' ) ?>
                                         </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="small d-inline-block text-end position-relative">
                <div class="position-absolute bottom-0 end-0">
                    <span class="d-inline-block pe-3 pb-1 text-small">
                        DB:  <b class="strong-font-weight text-danger d-inline-block pe-1">v<?=GOOGLE_REZENSIONEN_API_DB_VERSION?></b>
                        Version: <b class="strong-font-weight text-danger">v<?=$this->version?></b>
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
<div id="snackbar-success"></div>
<div id="snackbar-warning"></div>