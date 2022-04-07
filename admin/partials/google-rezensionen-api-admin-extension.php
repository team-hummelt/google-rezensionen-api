<div class="wp-bs-starter-wrapper">
    <div class="container">
        <div class="card shadow-sm">
            <h5 data-type="close_command" id="GoogleRezensionRestApiConnect"
                class="card-header d-flex align-items-center bg-google py-4">
                <i class="fa fa-google"></i> &nbsp;
                <?= __('Google Rezensionen', 'google-rezensionen-api') ?> /&nbsp;
                <span class="currentSideTitle"> <?= __('Extensions', 'google-rezensionen-api') ?></span></h5>

            <div id="publicApiSettings"></div>
            <div id="cardFormulareWrapper" class="card-body pb-4" style="min-height: 72vh">

               <div class="d-flex align-items-center extension-overview">
                    <h5 class="card-title">
                        <i class="font-blue bi bi-subtract"></i> <?= __('Google Rezensionen', 'google-rezensionen-api') ?>
                        <span id="currentSideTitle"><?= __('Extensions', 'google-rezensionen-api') ?></span>
                    </h5>
                    <button data-bs-toggle="collapse" data-bs-target="#extensionTemplates"
                            title="<?= __('back to the overview', 'google-rezensionen-api') ?>"
                            class="btn-extension-back ms-auto btn btn-outline-light border d-none">
                        <i class="font-blue fst-normal fw-bold bi bi-x-lg"></i>
                    </button>
                </div>
                <hr>
                <div id="extensionCollapseParent">
                    <div id="extensionTemplates" class="collapse show"
                         data-bs-parent="#extensionCollapseParent">
                        <div id="twigRenderOverview"></div>
                    </div>
                    <div class="collapse"
                         data-bs-parent="#extensionCollapseParent"
                         id="extensionDetails">
                        <div id="twigRenderDetails"></div>
                    </div>
                    <div class="collapse"
                         data-bs-parent="#extensionCollapseParent"
                         id="extensionDownload">
                        <div id="twigRenderDownload"></div>
                    </div>
                    <div class="collapse collapseLicense"
                         data-bs-parent="#extensionCollapseParent"
                         id="extensionActivate">
                        <div id="twigRenderActivate"></div>
                    </div>

                    <div id="extensionLicense" class="collapseLicense collapse"
                         data-bs-parent="#extensionCollapseParent">
                        <div id="twigRenderLicense"></div>
                    </div>
                    <div id="extensionRestHelp" class="collapseLicense collapse"
                         data-bs-parent="#extensionCollapseParent">
                        <div id="twigRenderHelp"></div>
                    </div>
                </div>

                <div id="apiErrMsg" class="card shadow-sm m-5 d-none">
                    <h5 class="card-header text-center py-5">
                        <i class="font-blue bi bi-hourglass-split spin-deg"></i>&nbsp;
                        <?= __('Extensions are being updated and will be available again shortly.', 'google-rezensionen-api') ?>
                    </h5>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="snackbar-success"></div>
<div id="snackbar-warning"></div>