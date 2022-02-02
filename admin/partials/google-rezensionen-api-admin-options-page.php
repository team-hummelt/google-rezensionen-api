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
$value = json_decode($dbData->app_settings);
?>
<div class="wp-bs-starter-wrapper">
    <div class="container">
        <div class="card  shadow-sm">

            <h5 class="card-header d-flex align-items-center bg-google py-4">
                <i class="fa fa-google"></i> &nbsp;
				<?= __( 'Google Rezensionen', 'google-rezensionen-api' ) ?>
            </h5>

            <div class="card-body pb-4" style="min-height: 72vh">
                <div class="d-flex align-items-center">
                    <h5 class="card-title">
                        <i class="text-google fa fa-gears"></i>
                        <span class="currentSideTitle">
					<?= __( 'Google API', 'google-rezensionen-api' ) ?>	<?= __( 'Settings', 'google-rezensionen-api' ) ?>
                        </span>
                    </h5>
                    <div class="ajax-status-spinner ms-auto d-inline-block mb-2 pe-2"></div>
                </div>
                <hr>
                <div class="col-xl-8 col-lg-10 col-12 mx-auto pt-4">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title pb-3">
								<?= __( 'Google Business API Settings', 'google-rezensionen-api' ) ?>
                                <sup class="sup-style">(1)</sup>
                            </h5>
                            <form class="google-api-formular-auto-safe">
                                <input type="hidden" name="method" value="google_api_options_handle">
                                <div class="row g-2">
                                    <div class="col-xl-6 col">
                                        <div class="mb-3">
                                            <label for="inputGmapsApiKey" class="strong-font-weight form-label mb-1">
												<?= __( 'Google API Key', 'google-rezensionen-api' ) ?>
                                            </label>
                                            <input type="text" value="<?=$value->google_api_key?>" name="api_key" class="form-control no-blur"
                                                   id="inputGmapsApiKey"
                                                   aria-describedby="apiKeyHelp">
                                            <div id="apiKeyHelp" class="form-text"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col">
                                        <div class="mb-3">
                                            <label for="inputGoogleApiUrl" class="strong-font-weight form-label mb-1">
												<?= __( 'Google API URL', 'google-rezensionen-api' ) ?>
                                            </label>
                                            <input type="url" value="<?=$value->google_api_url?>" name="api_url"
                                                   class="form-control no-blur" id="inputGoogleApiUrl"
                                                   aria-describedby="apiUrlHelp" readonly>
                                            <div id="apiUrlHelp" class="form-text"></div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mt-3 strong-font-weight">
		                            <?= esc_html__( 'Privacy settings', 'google-rezensionen-api' ) ?>
                                </h6>
                                <div class="form-check form-switch">
                                    <input onclick="this.blur()" class="form-check-input" name="ds_aktiv"
                                           type="checkbox" role="switch" id="dsChecked" <?=!$value->google_ds_show ?: 'checked'?>>
                                    <label class="form-check-label" for="dsChecked">
			                            <?= __( 'Google Maps privacy active', 'google-rezensionen-api' ) ?>
                                    </label>
                                </div>
                                <hr>
                                <h6 class="mt-3 strong-font-weight">
		                            <?= esc_html__( 'Search field Settings', 'google-rezensionen-api' ) ?>
                                </h6>
                                <div class="form-check form-switch pe-none">
                                    <input onclick="this.blur()" class="form-check-input" name="completion_aktiv"
                                           type="checkbox" role="switch" id="CompletionChecked" <?=!$value->completion_aktiv ?: 'checked'?> disabled>
                                    <label class="form-check-label" for="CompletionChecked">
			                            <?= __( 'Automatic completion active', 'google-rezensionen-api' ) ?>
                                    </label>
                                  </div>
                                <div class="form-text text-danger mt-2">
                                    <i class="fa fa-exclamation-triangle"></i>&nbsp;
	                                <?= __( 'Auto-completion is not available in this plugin version.', 'google-rezensionen-api' ) ?>
                                </div>
                                <hr>
                                <h6 >
                                    <i class="font-blue fa fa-arrow-circle-down"></i>
                                    <?= esc_html__( 'Minimum requirement for using this function', 'google-rezensionen-api' ) ?>
                                </h6>
                                <hr>
                                <div class="row pt-3 g-2">
                                    <div class="col-xl-6 col">
                                        <div class="mb-3">
                                            <label for="capabilitySelect"
                                                   class="form-label mb-1 strong-font-weight"><?= esc_html__( 'User Role', 'google-rezensionen-api' ) ?>
                                            </label>
                                            <select name="user_role"
                                                    id="capabilitySelect" class="form-select no-blur">
												<?php
												$select   = apply_filters( $this->basename . '/google_api_selects', 'user_role' );
												$settings = apply_filters( $this->basename . '/get_settings', 'user_capability' );
												foreach ( $select as $key => $val ):
													$key == $settings->user_capability ? $sel = 'selected' : $sel = ''; ?>
                                                    <option value="<?= $key ?>"<?= $sel ?>><?= $val ?></option>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </form>
                            <hr>
                            <div class="form-text d-flex mb-3">
                                <span class="text-danger strong-font-weight text-nowrap d-inline-block sup-style h-100" style="min-width: 24px">(1) </span>
                                <span>
                                    <?php
                                    $url = '<a target="_blank" class="link-neutral text-decoration-none strong-font-weight" 
                                            href="https://developers.google.com/my-business/content/basic-setup">'; ?>
                                    <?= sprintf( __( 'The Google My Business API is an automated process that allows authorised people to manage business location data for Google Maps. Learn how to create an API key %s here</a>.', 'google-rezensionen-api' ), $url ) ?>
                                </span>
                            </div>
                            <div class="response-alert"></div>
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