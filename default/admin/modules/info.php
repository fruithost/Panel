<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */
	
	use fruithost\Localization\I18N;
?>
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
						<?php I18N::__('From'); ?> <span class="module-author"></span> | <span class="module-url"></span>
                    </p>
                    <p class="module-description"></p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item d-none details-tab" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button" role="tab" aria-controls="details-pane" aria-selected="true"><?php I18N::__('Details'); ?></button>
            </li>
            <li class="nav-item d-none features-tab" role="presentation">
                <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features-pane" type="button"  role="tab" aria-controls="features-pane" aria-selected="false"><?php I18N::__('Features'); ?></button>
            </li>
            <li class="nav-item d-none screenshots-tab" role="presentation">
                <button class="nav-link" id="screenshots-tab" data-bs-toggle="tab" data-bs-target="#screenshots-pane" type="button" role="tab" aria-controls="screenshots-pane" aria-selected="false"><?php I18N::__('Screenshots'); ?></button>
            </li>
            <li class="nav-item d-none changelog-tab" role="presentation">
                <button class="nav-link" id="changelog-tab" data-bs-toggle="tab" data-bs-target="#changelog-pane" type="button" role="tab" aria-controls="changelog-pane" aria-selected="false"><?php I18N::__('Changelog'); ?></button>
            </li>
        </ul>

        <!-- Content -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade markdown" id="details-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0"></div>
            <div class="tab-pane fade" id="screenshots-pane" role="tabpanel" aria-labelledby="screenshots-tab" tabindex="0">
                <div id="screenshotHolder" class="carousel slide carousel-dark">
                    <div class="carousel-inner"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#screenshotHolder" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php I18N::__('Previous'); ?></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#screenshotHolder" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php I18N::__('Next'); ?></span>
                    </button>
                </div>

            </div>
            <div class="tab-pane fade" id="features-pane" role="tabpanel" aria-labelledby="features-tab" tabindex="0"></div>
            <div class="tab-pane fade markdown" id="changelog-pane" role="tabpanel" aria-labelledby="changelog-tab" tabindex="0"></div>
        </div>
    </div>
</div>