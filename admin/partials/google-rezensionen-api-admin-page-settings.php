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

$dbData = apply_filters( $this->basename . '/get_settings', 'app_settings' );
$value  = json_decode( $dbData->app_settings );
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
						<?= __( 'Google Business API', 'google-rezensionen-api' ) ?> <?= __( 'Extensions', 'google-rezensionen-api' ) ?>
					</h5>
					<div class="card-body" style="min-height: 60vh">
						<div class="no-extension pt-5 mt-5">
							<h5 class="text-center">
								<i class="wp-color fa fa-info"></i>
								<?= __( 'There are currently no extensions available.', 'google-rezensionen-api' ) ?>
							</h5>
						</div>
					</div>
				</div>
			</div>
			<small class="small d-inline-block text-end position-relative">
				<div class="position-absolute bottom-0 end-0">
                    <span class="d-inline-block pe-3 pb-1 text-small">
                        DB:  <b class="strong-font-weight text-danger d-inline-block pe-1">v<?=GOOGLE_REZENSIONEN_API_DB_VERSION?></b>
                        Version: <b class="strong-font-weight text-danger">v<?=$this->version?></b>
                    </span>
				</div>
			</small>
		</div>
	</div>
</div>
<div id="snackbar-success"></div>
<div id="snackbar-warning"></div>