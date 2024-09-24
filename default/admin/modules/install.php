<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */
	
	use fruithost\Localization\I18N;
	use fruithost\UI\Icon;
?>
<div id="module-results" data-fetching="true">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden"><?php I18N::__('Loading'); ?>...</span>
    </div>
    <div class="loaded-content tab-content">
        <div class="container mt-5 mb-5 tab-pane show active" id="popular" role="tabpanel" aria-labelledby="popular-tab">
            <div class="row row-cols-1 row-cols-md-3 g-4 modules-list"></div>
        </div>
        <div class="container mt-5 mb-5 tab-pane" id="results" role="tabpanel" aria-labelledby="results-tab">
            <div class="row row-cols-1 row-cols-md-3 g-4 results-list"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (() => {
        'use strict';

        function search(query) {
            document.querySelector('#module-results').dataset.fetching = true;
            let container = document.querySelector('div.modules-list');
            let results = document.querySelector('div.results-list');
            let template = document.querySelector('template#module-entry');
            let no_results = document.querySelector('template#no_results');
            let res = document.querySelector('.modules-results a.nav-link');
            let url = document.querySelector('input[name="url"]').value;
            if (typeof (query) === 'undefined') {
                query = null;
            }

            if (query == null) {
                container.innerHTML = '';
            } else {
                results.innerHTML = '';
                res.ariaDisabled = false;
                res.classList.remove('disabled');
            }

            new Ajax('/ajax').onSuccess((response) => {
                if (Object.keys(response).length == 0) {
                    const entry = no_results.content.cloneNode(true);

                    if (query == null) {
                        container.classList.remove('row-cols-1');
                        container.classList.remove('row-cols-md-3');
                        container.classList.remove('g-4');
                        container.appendChild(entry);
                    } else {
                        results.classList.remove('row-cols-1');
                        results.classList.remove('row-cols-md-3');
                        results.classList.remove('g-4');

                        entry.querySelector('.lead').innerHTML = '<?php I18N::__('Searching for "%s" did not return any results.'); ?>'.replace('%s', '<strong>' + query + '</strong>');
                        results.appendChild(entry);
                    }
                } else {
                    if (query == null) {
                        container.classList.add('row-cols-1');
                        container.classList.add('row-cols-md-3');
                        container.classList.add('g-4');
                    } else {
                        results.classList.add('row-cols-1');
                        results.classList.add('row-cols-md-3');
                        results.classList.add('g-4');

                    }
                    Object.keys(response).forEach((name) => {
                        let module = response[name];
                        const entry = template.content.cloneNode(true);

                        entry.querySelector('div.module-icon').innerHTML = module.icon;
                        entry.querySelector('a.module-name').innerText = module.name;
                        entry.querySelector('p.module-description').innerText = module.description;
                        entry.querySelector('span.module-version').innerText = module.version;
                        entry.querySelector('span.module-author').innerHTML = '<a href="mailto:' + module.author.email + '" class="text-decoration-none" target="_blank">' + module.author.name + '</a>';
                        entry.querySelector('span.module-web').innerHTML = '<a href="' + module.author.url + '" class="text-decoration-none" target="_blank">' + module.author.url + '</a>';

                        /* Buttons */
                        let buttons = [];
                        if (module.installed) {
                            let deinstall = document.createElement('a');
                            deinstall.href = url + '?deinstall=' + name;
                            deinstall.classList.add('btn');
                            deinstall.classList.add('btn-sm');
                            deinstall.classList.add('btn-outline-danger');
                            deinstall.dataset.confirm = '<?php I18N::__('Do you really wan\\\'t to delete this Module?'); ?>';
                            deinstall.innerText = '<?php I18N::__('Deinstall'); ?>';
                            buttons.push(deinstall);
                            Confirm(deinstall);
                        } else {
                            let install = document.createElement('a');
                            install.href = url + '?install=' + name;
                            install.classList.add('btn');
                            install.classList.add('btn-sm');
                            install.classList.add('btn-success');
                            install.innerText = '<?php I18N::__('Install'); ?>';
                            install.dataset.loading = '<?php I18N::__('Installing'); ?>';
                            buttons.push(install);
                            Loading(install);
                        }

                        let info = document.createElement('button');
                        info.type = 'button';
                        info.name = 'module_info';
                        info.classList.add('btn');
                        info.classList.add('btn-sm');
                        info.classList.add('btn-outline-light');
                        info.innerText = '<?php I18N::__('Info'); ?>';
                        info.dataset.module = name;
                        buttons.push(info);
                        info.addEventListener('click', (event) => {
                            new bootstrap.Modal('#module_info').toggle(event.target);
                        });

                        buttons.forEach((button) => {
                            entry.querySelector('div.module-buttons').appendChild(button);
                        });

                        if (query == null) {
                            container.appendChild(entry);
                        } else {
                            results.appendChild(entry);
                        }
                    });
                }

                if (query != null) {
                    new bootstrap.Tab(res).show();
                }
                document.querySelector('#module-results').dataset.fetching = false;
            }).post({
                'search': query
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            search();

            document.querySelector('div#module-search input[name="query"]').addEventListener('keypress', (event) => {
                if (event.which == 13) {
                    event.preventDefault();
                    search(event.target.value);
                    event.target.value = '';
                    return false;
                }
            });

            document.querySelector('div#module-search button[type="button"]').addEventListener('click', (event) => {
                event.preventDefault();

                search(event.target.value);

                return false;
            });
        });
    })();
</script>
<input type="hidden" name="url" value="<?php print $this->url('/admin/modules/'); ?>"/>
<template id="no_results">
    <div class="jumbotron text-center bg-transparent text-muted">
		<?php Icon::show('smiley-bad'); ?>
        <h2><?php I18N::__('No Modules available!'); ?></h2>
        <p class="lead"></p>
    </div>
</template>
<template id="module-entry">
    <div class="col">
        <div class="card">
            <div class="card-body row">
                <div class="col-2 fs-1 module-icon"></div>
                <div class="col-6">
                    <h5 class="card-title"><a class="module-name"></a></h5>
                    <p style="height: 120px;" class="card-text module-description"></p>
                </div>
                <div class="col-4">
                    <div class="d-grid gap-2 mx-auto module-buttons"></div>
                </div>
            </div>
            <div class="card-footer text-body-secondary">
                <div class="row">
                    <div class="col small text-nowrap">
						<?php I18N::__('From'); ?> <span class="module-author"></span> | <span class="module-web"></span>
                    </div>
                    <div class="col text-end">
                        <span class="badge text-bg-secondary module-version"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>