<?php
	
	use fruithost\Localization\I18N;

?>
<style>
    div#module_info .module-icon {
        font-size: 100px;
    }

    div#module_info .carousel-caption {
        right: 0;
        bottom: 0;
        left: 0;
        color: #fff !important;
        background: rgba(0, 0, 0, 0.5);
    }

    div#module_info .modal-loading[data-fetching="true"] {
        display: flex;
        justify-content: center !important;
    }

    div#module_info .modal-loading[data-fetching="true"] [role="status"] {
        display: block;
        width: 5rem;
        height: 5rem;
        margin: 50px 0;
    }

    div#module_info .modal-loading[data-fetching="false"] [role="status"] {
        display: none;
    }

    div#module_info .modal-loading[data-fetching="true"] .loaded-content {
        display: none;
    }

    div#module_info .modal-loading[data-fetching="false"] .loaded-content {
        display: block;
    }

    div#module_info div.markdown {
        background: var(--bs-modal-footer-border-color);
        padding: 20px;
        font-size: 16px;
    }

    div#module_info div.markdown h1 {
        font-size: 1.4rem;
    }

    div#module_info div.markdown h2 {
        font-size: 1.3rem;
    }

    div#module_info div.markdown h3 {
        font-size: 1.2rem;
    }

    div#module_info div.markdown h4 {
        font-size: 1.1rem;
    }

    div#module_info div.markdown h5 {
        font-size: 1.0rem;
    }

    div#module_info div.markdown h6 {
        font-size: 0.9rem;
    }
</style>
<div class="modal-loading" data-fetching="true">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden"><?php I18N::__('Loading'); ?>...</span>
    </div>
    <div class="loaded-content">
        <!-- Header -->
        <div class="container">
            <div class="row">
                <div class="col col-2 module-icon"></div>
                <div class="col">
                    <h4><span class="module-name"></span> <span class="badge text-bg-light module-version"></span></h4>
                    <p class="small text-nowrap">
						<?php I18N::__('From'); ?> <span class="module-author"></span> | <span
                                class="module-url"></span>
                    </p>
                    <p class="module-description"></p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item d-none details-tab" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane"
                        type="button"
                        role="tab" aria-controls="details-pane" aria-selected="true"><?php I18N::__('Details'); ?>
                </button>
            </li>
            <li class="nav-item d-none features-tab" role="presentation">
                <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features-pane"
                        type="button"
                        role="tab" aria-controls="features-pane" aria-selected="false"><?php I18N::__('Features'); ?>
                </button>
            </li>
            <li class="nav-item d-none screenshots-tab" role="presentation">
                <button class="nav-link" id="screenshots-tab" data-bs-toggle="tab" data-bs-target="#screenshots-pane"
                        type="button"
                        role="tab" aria-controls="screenshots-pane"
                        aria-selected="false"><?php I18N::__('Screenshots'); ?>
                </button>
            </li>
            <li class="nav-item d-none changelog-tab" role="presentation">
                <button class="nav-link" id="changelog-tab" data-bs-toggle="tab" data-bs-target="#changelog-pane"
                        type="button"
                        role="tab" aria-controls="changelog-pane" aria-selected="false"><?php I18N::__('Changelog'); ?>
                </button>
            </li>
        </ul>

        <!-- Content -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade markdown" id="details-pane" role="tabpanel" aria-labelledby="details-tab"
                 tabindex="0"></div>
            <div class="tab-pane fade" id="screenshots-pane" role="tabpanel" aria-labelledby="screenshots-tab"
                 tabindex="0">
                <div id="screenshotHolder" class="carousel slide carousel-dark">
                    <div class="carousel-inner"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#screenshotHolder"
                            data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php I18N::__('Previous'); ?></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#screenshotHolder"
                            data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php I18N::__('Next'); ?></span>
                    </button>
                </div>

            </div>
            <div class="tab-pane fade" id="features-pane" role="tabpanel" aria-labelledby="features-tab"
                 tabindex="0"></div>
            <div class="tab-pane fade markdown" id="changelog-pane" role="tabpanel" aria-labelledby="changelog-tab"
                 tabindex="0"></div>
        </div>
    </div>
</div>