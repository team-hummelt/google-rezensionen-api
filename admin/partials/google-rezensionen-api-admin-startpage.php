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

$lang   = substr( get_bloginfo( 'language' ), 0, 2 );
$dbData = apply_filters( $this->basename . '/get_settings', '' );
$dbData->app_settings->google_api_key ? $status = true : $status = false;
$api_sync_settings = $dbData->api_sync_settings;

?>
<div class="wp-bs-starter-wrapper">
    <div class="container">
        <div class="card shadow-sm">
            <h5 class="card-header d-flex align-items-center bg-google py-4">
                <i class="fa fa-google"></i> &nbsp;
				<?= __( 'Google Rezensionen', 'google-rezensionen-api' ) ?> /&nbsp;
                <span class="currentSideTitle"> <?= __( 'Overview', 'google-rezensionen-api' ) ?></span></h5>
            <div class="card-body pb-4" style="min-height: 72vh">
                <div id="apiErrMsg" class="card shadow-sm m-5 d-none">
                    <h5 class="card-header text-center py-5">
                        <i class="font-blue fa fa-hourglass-half spin-deg"></i>&nbsp;
						<?= __( 'Extensions are being updated and will be available again shortly.', 'google-rezensionen-api' ) ?>
                    </h5>
                </div>

                <div class="d-flex align-items-center">
                    <h5 class="card-title">
                        <i class="text-google fa fa-arrow-circle-right"></i>
                        <span class="currentSideTitle">
						<?= __( 'Overview', 'google-rezensionen-api' ) ?>
                        </span>
                    </h5>
                </div>
                <hr>
                <div class="d-flex flex-wrap">
                    <button data-bs-site="overview" data-bs-title="<?= __( 'Overview', 'google-rezensionen-api' ) ?>"
                            data-bs-toggle="collapse" data-bs-target="#overviewTemplates"
                            class="btn btn-outline-secondary btn-collapse btn-google-api active me-1 btn-sm">
                        <i class="fa fa-google"></i>&nbsp; <?= __( 'Overview', 'google-rezensionen-api' ) ?>
                    </button>
                    <button data-bs-site="settings" data-bs-title="<?= __( 'Settings', 'google-rezensionen-api' ) ?>"
                            data-bs-toggle="collapse" data-bs-target="#settingsTemplate"
                            class="btn btn-outline-secondary btn-collapse btn-google-api me-1 btn-sm">
                        <i class="fa fa-gears"></i>&nbsp; <?= __( 'Settings', 'google-rezensionen-api' ) ?>
                    </button>
                </div>
                <hr>
                <div id="googleApiCollapseParent">
                    <div id="overviewTemplates" class="collapse show"
                         data-bs-parent="#googleApiCollapseParent">
                        <button data-template="add_google_recension"
                                class="btnLoad <?= $dbData->app_settings->google_ds_show ? 'btn-show-ds' : 'btn-load-step1' ?>
                                btn btn-blue-outline btn-sm" <?= $status ? '' : 'disabled' ?>>
                            <i class="fa fa-google"></i>
                            <span class="btn-api-spinner spinner-border spinner-border-sm" role="status"
                                  aria-hidden="true"></span>
                            &nbsp;<?= __( 'Create a new review', 'google-rezensionen-api' ) ?>
                        </button>
                        <hr>
                        <div class="row g-2" id="overview-data-template"></div>
                    </div>

                    <!--//JOB REZENSION SETTINGS BY ID-->
                    <div class="collapse" data-bs-parent="#googleApiCollapseParent"
                         id="showSettingsById">
                        <div id="twig-settings-template"></div>
                    </div>

                    <!--//JOB GMAPS PLACE ID STEP 1 COLLAPSE-->
                    <div class="collapse <?= $status ? '' : 'd-none' ?>" data-bs-parent="#googleApiCollapseParent"
                         id="showPlaceIdStepOne">
                        <div class="card shadow-sm mb-3 mt-5 col-xl-6 col-lg-10 col-12 mx-auto"
                             style="min-height: 30vh">
                            <h5 class="card-header d-flex align-items-center">
                                    <span>
                                    <i class="text-google fa fa-google"></i>&nbsp;
                                    <?= __( 'Create review Step', 'google-rezensionen-api' ) ?> 1
                                        </span>
                                <button class="cancelDsBtn btn btn-outline-light ms-auto border">
                                    <i class="wp-color fa fa-close"></i>
                                </button>
                            </h5>
                            <div class="card-body">
                                <div class="card-title text-center pt-3">
                                    <b><?= __( 'A Place ID is required to retrieve reviews.', 'google-rezensionen-api' ) ?></b><br>
                                    <!-- <small class="small d-block px-3 mt-2" style="font-size: .8rem">
										<?= __( 'Enter the Place ID in the field and click Submit. Or, click Search Place ID to search for the place ID.', 'google-rezensionen-api' ) ?>
                                    </small>-->
                                </div>


                                <div class="input-group mt-3">
                                    <input type="text" class="form-control no-blur"
                                           placeholder="<?= __( 'Enter Place ID', 'google-rezensionen-api' ) ?>"
                                           aria-label="<?= __( 'Enter Place ID', 'google-rezensionen-api' ) ?>"
                                           aria-describedby="sendPlaceID">
                                    <button class="btn btn-blue" type="button"
                                            id="sendPlaceID"><?= __( 'Send', 'google-rezensionen-api' ) ?>&nbsp; <i
                                                class="fa fa-angle-right"></i>
                                    </button>
                                </div>
                                <div class="form-text">
									<?= __( 'If you do not know the Place ID, click Search Place ID.', 'google-rezensionen-api' ) ?>
                                </div>

                                <div class="d-lg-flex d-block flex-wrap mt-4">

                                    <button class="btnSearchPlaceID btn btn-blue-outline btn-sm me-1">
                                        <i class="fa fa-search"></i>&nbsp; <?= __( 'Place ID search', 'google-rezensionen-api' ) ?>
                                    </button>

                                    <button class="cancelDsBtn btn btn-outline-light border text-body btn-sm ms-auto me-1">
                                        <i class="text-danger fa fa-close"></i>&nbsp; <?= __( 'Cancel', 'google-rezensionen-api' ) ?>
                                    </button>
                                </div>
                            </div><!--body-->
                        </div><!--Card-->
                    </div><!--Collapse-->

                    <!--//JOB GMAPS PLACE ID SEARCH COLLAPSE-->
                    <div class="collapse <?= $status ? '' : 'd-none' ?>" data-bs-parent="#googleApiCollapseParent"
                         id="searchPlaceId">
                        <div class="card shadow-sm mb-3 mt-5 col-xl-10 col-12 mx-auto"
                             style="min-height: 30vh">
                            <h5 class="card-header d-flex align-items-center">
                                    <span>
                                    <i class="text-google fa fa-google"></i>&nbsp;
                                    <?= __( 'Place ID', 'google-rezensionen-api' ) ?> <?= __( ' search', 'google-rezensionen-api' ) ?>
                                    </span>

                                <button class="btn-load-step1 btn btn-outline-light ms-auto me-2 border">
                                    <i class="wp-color fa fa-mail-reply-all"></i>
                                </button>
                                <button class="cancelDsBtn btn btn-outline-light  border">
                                    <i class="wp-color fa fa-close"></i>
                                </button>
                            </h5>
                            <div class="card-body">
                                <div class="col-xl-7 col-lg-10 col-12 mx-auto">
                                    <div class="card-title text-center py-2">
                                        <b><?= __( 'A Place ID is required to retrieve reviews.', 'google-rezensionen-api' ) ?></b><br>
                                        <small class="small d-block mt-2" style="font-size: .8rem">
											<?= __( '<b class="strong-font-weight">How it works:</b> Simply enter an address, place, point of interest or business in the search box. Press Enter or the button and the plugin should find and display the entry.', 'google-rezensionen-api' ) ?>
                                        </small>
                                    </div>
                                </div>
                                <hr>
								<?php if ( $dbData->app_settings->completion_aktiv ): ?>
                                    <div id="searchPlaceIDParent">
                                        <div id="countriesSearchColl" class="collapse show"
                                             data-bs-parent="#searchPlaceIDParent">
                                            <h6><i class="wp-color fa fa-search"></i>&nbsp;
												<?= __( 'In which country would you like to search', 'google-rezensionen-api' ) ?>
                                            </h6>
                                            <div class="input-group mb-3  bg-custom-gray rounded no-dataset-caret p-4">
                                                <div class="data-set-select-icon flex-fill" style="margin-right: -2rem">
                                                    <input class="form-control no-blur" list="datalistOptions"
                                                           id="selectCountry"
                                                           aria-describedby="selectCountry"
                                                           aria-label="Select Country"
                                                           placeholder="<?= __( 'Type to search', 'google-rezensionen-api' ) ?>...">

                                                    <datalist id="datalistOptions">
														<?php
														$countries = apply_filters( $this->basename . '/get_countries_select', '' );
														if ( $countries->status ): foreach ( $countries->record as $tmp ):
														$key = key( $tmp ); ?>
                                                        <option value="<?= $tmp->code ?> - <?= $tmp->$key ?>">
															<?php endforeach;
															endif; ?>
                                                    </datalist>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <i class="reset-dataset-form fa fa-close pe-3 cursor-pointer wp-color"></i>
                                                    <span>
                                            <button class="btn btn-blue"
                                                    type="button" id="sendCountryData">
                                                <i class="fa fa-search"></i>&nbsp;
												<?= __( 'Select', 'google-rezensionen-api' ) ?>
                                            </button>
                                           </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="searchGoogleTextSearch" class="collapse"
                                             data-bs-parent="#searchPlaceIDParent">
                                            <h6>
                                                <i class="wp-color fa fa-search ms-1"></i>&nbsp;
												<?= __( 'Google Search: Enter company, name or address', 'google-rezensionen-api' ) ?>
                                            </h6>
                                            <form class="dynamic_forms_input" autocomplete="off">
                                                <input type="hidden" name="method" value="get_details_by_place_id">
                                                <input class="inputTypeCountry" type="hidden" name="country">

                                                <div class="input-group bg-custom-gray rounded p-4 mb-4">
                                                    <div class="autocomplete position-relative flex-fill">
                                                        <input type="text" name="search"
                                                               class="input_search form-control no-blur"
                                                               id="search_completion"
                                                               placeholder="<?= __( 'Enter an address, shop or place of interest', 'google-rezensionen-api' ) ?>"
                                                               aria-label="Search"
                                                               aria-describedby="placeIDInputSearchBtn"></div>
                                                    <button class="btn btn-blue ms-auto"
                                                            type="submit" id="placeIDInputSearchBtn">
														<?= __( 'Select', 'google-rezensionen-api' ) ?>&nbsp; <i
                                                                class="fa fa-angle-right"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div><!--collapse-->
                                    </div><!--parent-->
								<?php else: ?>
                                    <div class="input-group bg-custom-gray rounded p-4 mb-4">
                                        <input type="text" class="form-control no-blur"
                                               placeholder="<?= __( 'Enter an address, shop or place of interest', 'google-rezensionen-api' ) ?>"
                                               aria-label="Search" aria-describedby="placeIDGoogleInputSearchBtn">
                                        <button class="btn btn-blue"
                                                type="button" id="placeIDGoogleInputSearchBtn">
											<?= __( 'Search', 'google-rezensionen-api' ) ?>&nbsp; <i
                                                    class="fa fa-angle-right"></i>
                                        </button>
                                    </div>
								<?php endif; ?>
                                <!-- MAP-->
                                <div class="googleMap">
                                    <div id="map"></div>
                                </div>
                                <!-- MAP-->
                                <hr>
                                <button class="cancelDsBtn btn btn-outline-light border text-body btn-sm ms-auto me-1">
                                    <i class="text-danger fa fa-close"></i>&nbsp; <?= __( 'Cancel', 'google-rezensionen-api' ) ?>
                                </button>
                            </div><!--body-->
                        </div><!--Card-->
                    </div><!--Collapse-->

                    <!--//JOB RESULT SEARCH COLLAPSE-->
                    <div class="collapse <?= $status ? '' : 'd-none' ?>" data-bs-parent="#googleApiCollapseParent"
                         id="resultPlaceId">
                        <div class="card shadow-sm mb-3 mt-5 col-xl-10 col-12 mx-auto"
                             style="min-height: 30vh">
                            <h5 class="card-header d-flex align-items-center">
                                    <span>
                                    <i class="text-google fa fa-google"></i>&nbsp;
                                    <?= __( 'Place ID', 'google-rezensionen-api' ) ?> <?= __( ' search', 'google-rezensionen-api' ) ?>
                                        </span>

                                <button class="btn-load-step1 btn btn-outline-light me-2 ms-auto border">
                                    <i class="wp-color fa fa-mail-reply-all"></i>
                                </button>
                                <button class="cancelDsBtn btn btn-outline-light border">
                                    <i class="wp-color fa fa-close"></i>
                                </button>
                            </h5>
                            <div class="card-body">
                                <div class="col-xl-7 col-lg-10 col-12 mx-auto">
                                    <div class="card-title text-center py-2">
                                        <b><?= __( 'A Place ID is required to retrieve reviews.', 'google-rezensionen-api' ) ?></b><br>
                                        <small class="small d-block mt-2" style="font-size: .8rem">
											<?= __( '<b class="strong-font-weight">How it works:</b> Simply enter an address, place, attraction or business in the search box and press enter or the button and the tool should output the Place ID.', 'google-rezensionen-api' ) ?>
                                        </small>
                                    </div>
                                </div>
                                <hr>
                                <div class="render-result-place-id"></div>
                            </div>
                        </div><!--card-->
                    </div><!--collapse-->

                    <!--//JOB GMAPS Datenschutz COLLAPSE-->
                    <div class="collapse <?= $status ? '' : 'd-none' ?>" data-bs-parent="#googleApiCollapseParent"
                         id="googleDS">
                        <div class="card shadow-sm mb-3 mt-5 col-xl-6 col-lg-10 col-12 mx-auto"
                             style="min-height: 30vh">
                            <h5 class="card-header d-flex align-items-center">
                                    <span>
                                    <i class="text-google fa fa-google"></i>&nbsp;
                                    <?= __( 'Google Maps', 'google-rezensionen-api' ) ?>
                                        </span>
                                <button class="cancelDsBtn btn btn-outline-light ms-auto border">
                                    <i class="wp-color fa fa-close"></i>
                                </button>
                            </h5>
                            <form class="google-api-form">
                                <input type="hidden" name="method" value="load_api_data">
                                <div class="card-body">
                                    <div class="card-title text-center py-3">
                                        <b><?= __( 'This plugin uses the Google Maps service.', 'google-rezensionen-api' ) ?></b><br>
                                        <small class="small" style="font-size: .75rem">
											<?= __( 'The provider is', 'google-rezensionen-api' ) ?>
                                            Google Ireland Limited („Google“), Gordon House, Barrow Street, Dublin 4,
                                            Irland.
                                        </small>
                                    </div>
                                    <hr>
                                    <div class="d-flex align-items-center justify-content-center flex-column py-3">
                                        <div class="form-check">
                                            <input class="gMapsCheckDS form-check-input" type="checkbox"
                                                   name="policy_checked"
                                                   id="policyChecked" required>
                                            <label class="form-check-label" for="policyChecked">
												<?= __( 'I accept the privacy ', 'google-rezensionen-api' ) ?>
                                                <a class="link-neutral text-decoration-none strong-font-weight"
                                                   target="_blank"
                                                   href="https://policies.google.com/privacy?hl=<?= $lang ?>">
													<?= __( 'policy', 'google-rezensionen-api' ) ?>
                                                </a>
                                            </label>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <button type="submit" data-template="add_google_recension"
                                                    class="btnDatenSchutzLoad btn-load-template btn btn-blue-outline me-1 my-4 btn-sm"
                                                    disabled>
                                                <i class="fa fa-google"></i>
                                                &nbsp;<?= __( 'Create a new review', 'google-rezensionen-api' ) ?>
                                            </button>
                                            <button type="button"
                                                    class="cancelDsBtn btn btn-outline-light border text-body me-1 my-4 btn-sm">
                                                <i class="text-danger fa fa-close"></i>&nbsp; <?= __( 'Cancel', 'google-rezensionen-api' ) ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input onclick="this.blur()" class="form-check-input form-check-input-sm"
                                               type="checkbox" name="disable_policy"
                                               id="checkNoDsShow">
                                        <label class="form-check-label" for="checkNoDsShow">
                                            <small class="small"> <?= __( 'no longer show', 'google-rezensionen-api' ) ?></small>
                                        </label>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--//JOB SETTINGS COLLAPSE-->
                <div class="collapse"
                     data-bs-parent="#googleApiCollapseParent"
                     id="settingsTemplate">
                    <div class="card shadow-sm mb-3 pb-3 mt-5 col-xl-10 col-12 mx-auto"
                         style="min-height: 30vh">
                        <h5 class="card-header d-flex align-items-center">
                                    <span>
                                    <i class="text-google fa fa-google"></i>&nbsp;
                                    <?= __( 'Google Review API Synchronisation Settings', 'google-rezensionen-api' ) ?>
                                        <small class="small">( <?= __( 'Static Map', 'google-rezensionen-api' ) ?> )</small>
                                    </span>
                        </h5>
                        <div class="card-body">
                            <form class="google-api-formular-auto-safe">
                                <input type="hidden" name="method" value="set_api_sync_settings">
                                <h6 class="card-title d-flex align-items-center">
                                    <span>
                                    <i class="wp-color fa fa-arrow-circle-down"></i>
									<?= __( 'Images and Maps Download Settings', 'google-rezensionen-api' ) ?>
                                    </span>
                                    <span class="ajax-status-spinner ms-auto d-inline-block fw-normal mb-2 pe-2"></span>
                                </h6>
                                <hr>
                                <div class="row g-2">
                                    <div class="col-xl-6 col-12 mb-4">
                                        <label for="staticRoadMap" class="form-label strong-font-weight mb-2">
											<?= __( 'Google Maps', 'google-rezensionen-api' ) ?>  <?= __( 'Map type', 'google-rezensionen-api' ) ?>
                                            <small class="small fw-normal">( <?= __( 'Static Map', 'google-rezensionen-api' ) ?>
                                                )</small>
                                        </label>
                                        <select class="form-select no-blur mw-100" name="google_map_type"
                                                id="staticRoadMap">
											<?php
											$select = apply_filters( $this->basename . '/google_api_selects', 'map_type_select' );
											foreach ( $select as $key => $val ) :
												$api_sync_settings->google_map_type == $key ? $sel = 'selected' : $sel = '';
												?>
                                                <option value="<?= $key ?>"<?= $sel ?>><?= $val ?></option>
											<?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <h6><i class="fa fa-info"></i>&nbsp; <?= __( 'Card size', 'google-rezensionen-api' ) ?>
                                    <small class="small fw-normal">( <?= __( 'Static Map', 'google-rezensionen-api' ) ?>
                                        )</small>
                                </h6>
                                <div class="row g-2">
                                    <div class="col-xl-6 col-12 mb-3">
                                        <label for="InputHorizontal" class="form-label strong-font-weight mb-2">
											<?= __( 'Card', 'google-rezensionen-api' ) ?> <?= __( 'Horizontal', 'google-rezensionen-api' ) ?>
                                            (px)
                                        </label>
                                        <input type="number" name="horizontal_size"
                                               value="<?= $api_sync_settings->horizontal_size ?>"
                                               class="form-control no-blur"
                                               id="InputHorizontal">
                                    </div>

                                    <div class="col-xl-6 col-12 mb-3">
                                        <label for="InputVertical" class="form-label strong-font-weight mb-2">
											<?= __( 'Card', 'google-rezensionen-api' ) ?>  <?= __( 'Vertical', 'google-rezensionen-api' ) ?>
                                            (px)
                                        </label>
                                        <input type="number" name="vertical_size"
                                               value="<?= $api_sync_settings->vertical_size ?>"
                                               class="form-control no-blur" id="InputVertical">
                                    </div>

                                    <div class="col-xl-6 col-12 mb-3">
                                        <label for="ImageFormat" class="form-label strong-font-weight mb-2">
											<?= __( 'Google Maps', 'google-rezensionen-api' ) ?>  <?= __( 'Image format', 'google-rezensionen-api' ) ?>
                                            <small class="small fw-normal">( <?= __( 'Static Map', 'google-rezensionen-api' ) ?>
                                                )</small>
                                        </label>
                                        <select class="form-select no-blur mw-100" name="map_image_format"
                                                id="ImageFormat">
											<?php
											$select = apply_filters( $this->basename . '/google_api_selects', 'map_image_format' );
											foreach ( $select as $key => $val ) :
												$api_sync_settings->map_image_format == $key ? $sel = 'selected' : $sel = '';
												?>
                                                <option value="<?= $key ?>"<?= $sel ?>><?= $val ?></option>
											<?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-6 col-12 mb-3">
                                        <label for="InputCardZoom" class="form-label strong-font-weight mb-2">
											<?= __( 'Card', 'google-rezensionen-api' ) ?>  <?= __( 'Zoom', 'google-rezensionen-api' ) ?>
                                        </label>
                                        <input type="number" name="static_card_zoom"
                                               value="<?= $api_sync_settings->static_card_zoom ?>"
                                               class="form-control no-blur" id="InputCardZoom">
                                    </div>
                                </div>
                                <div class="form-check mb-3 mt-2">
                                    <input onclick="this.blur()" class="form-check-input" name="scale2_aktiv"
                                           type="checkbox"
                                           id="CheckScale2"<?= $api_sync_settings->scale2_aktiv ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="CheckScale2">
										<?= __( 'Create high resolution image', 'google-rezensionen-api' ) ?>
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div><!--card-->
                </div><!--collapse-->
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

    <!-- Modal -->
    <div class="modal fade" id="rezensionInfoModal" tabindex="-1" aria-labelledby="rezensionInfoLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-google">
                    <h5 class="modal-title" id="rezensionInfoLabel"><i class="fa fa-google"></i>&nbsp;
                        <span> Modal title</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="text-center strong-font-weight">
						<?= __( 'Shortcode', 'google-rezensionen-api' ) ?>:
                        <span class="rezension-shortcode pt-2 small d-block">[google_rezension id="123"]</span>

                    </h5>
                    <hr>
                    <div class="d-flex align-items-center ">
                        <form class="google-api-formular-auto-safe">
                            <input type="hidden" name="method" value="update_rezension_aktiv">
                            <input class="place_id_input" type="hidden" name="id">
                            <div class="form-check form-switch">
                                <input onclick="this.blur()" name="aktiv"
                                       class="form-check-input rezension-aktiv" type="checkbox" role="switch"
                                       id="CheckRezensionAktiv">
                                <label class="form-check-label"
                                       for="CheckRezensionAktiv"><?= __( 'Show review', 'google-rezensionen-api' ) ?></label>
                            </div>

                            <hr>
                            <div class="strong-font-weight">
                                <i class="wp-color fa fa-arrow-circle-down"></i>&nbsp;
								<?= __( 'API synchronization settings', 'google-rezensionen-api' ) ?>
                                <small class="small">(<?= __( 'Updates', 'google-rezensionen-api' ) ?>)</small>
                                <sup class="text-danger">(1)</sup>
                            </div>
                            <hr>

                        </form>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-wrap align-items-center w-100">
                        <span class="ajax-status-spinner d-inline-block fw-normal"></span>
                        <span class="ms-auto d-block">
                        <button type="button" class="btn btn-outline-light border text-body" data-bs-dismiss="modal">
                    <i class="text-danger fa fa-close"></i>  <?= __( 'Close', 'google-rezensionen-api' ) ?>  </button>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rezensionDeleteModal" tabindex="-1" aria-labelledby="rezensionDeleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-hupa">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="fa fa-trash"></i> <?= __( 'Goggle Rezension', 'google-rezensionen-api' ) ?>  <?= __( 'delete', 'google-rezensionen-api' ) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">
                        <b class="text-danger"><?= __( 'Really delete review?', 'google-rezensionen-api' ) ?>?</b>
                        <small class="d-block"><?= __( 'This action can <b class="text-danger">not</b> be undone</b>! be undone!', 'google-rezensionen-api' ) ?></small>
                    </h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light text-body border" data-bs-dismiss="modal">
                        <i class="text-danger fa fa-times"></i>&nbsp; <?= __( 'Cancel', 'google-rezensionen-api' ) ?>
                    </button>
                    <button type="button" data-bs-dismiss="modal"
                            class="btn_delete_rezension btn btn-danger">
                        <i class="fa fa-trash-o"></i>&nbsp; <?= __( 'Delete', 'google-rezensionen-api' ) ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="snackbar-success"></div>
<div id="snackbar-warning"></div>