/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */
'use strict';

class ModuleInfo {
    constructor() {
        let modal = document.querySelector('div#module_info');

        if(modal) {
            modal.addEventListener('show.bs.modal', this.onShow.bind(this));
        }
    }

    error(modal, text) {
        // @ToDo Create Error
        console.warn('TPL-Error', text);
    }

    build(modal, response) {
        let tabs = [];
        let module      = response.info;

        if(typeof(response.info) == 'undefined') {
            return;
        }

        /* Header */
        try {
            if(module.name) {
                modal.querySelector('.modal-title').innerText = module.name;
            }

            if(module.icon) {
                modal.querySelector('.module-icon').innerHTML = module.icon;
            }

            if(module.name) {
                modal.querySelector('.module-name').innerText = module.name;
            }

            if(module.version) {
                modal.querySelector('.module-version').innerText = module.version;
            }

            if(module.description) {
                modal.querySelector('.module-description').innerText = module.description;
            }

            if(module.author) {
                if(module.author.email) {
                    if(module.author.email) {
                        modal.querySelector('.module-author').innerHTML = '<a href="mailto:' + module.author.email + '" class="text-decoration-none" target="_blank">' + module.author.name + '</a>';
                    } else if(module.author.name) {
                        modal.querySelector('.module-author').innerHTML = module.author.name;
                    } else {
                        modal.querySelector('.module-author').innerHTML = 'Unknown'; // @ToDo Language
                    }
                }

                if(module.author.url) {
                    modal.querySelector('.module-url').innerHTML = '<a href="' + module.author.url + '" class="text-decoration-none" target="_blank">' + module.author.url + '</a>';
                }
            }
        } catch(e) {
            console.error('[ERROR]', 'Can\'t handle Module-Infos:', e);
        }

        /* Readme */
        try {
            if(response.readme.length > 0) {
                tabs.push('details');
                modal.querySelector('.nav-item.details-tab').classList.remove('d-none');
                modal.querySelector('div#details-pane').innerHTML = response.readme;
            } else {
                modal.querySelector('div#details-pane').innerHTML = '';
            }
        } catch (e) {
            console.error('[ERROR]', 'Can\'t handle Module-Readme:', e);
        }

        /* Screenshots */
        try {
            if(response.screenshots.length > 0) {
                tabs.push('screenshots');
                let screenshots = modal.querySelector('div#screenshotHolder div.carousel-inner');
                modal.querySelector('.nav-item.screenshots-tab').classList.remove('d-none');
                screenshots.innerHTML = '';

                response.screenshots.forEach((entry, index) => {
                    let container = document.createElement('div');
                    container.classList.add('carousel-item');

                    if(index === 0) {
                        container.classList.add('active');
                    }

                    /* Image */
                    let image = new Image();
                    image.src = entry;
                    image.classList.add('d-block');
                    image.classList.add('w-100');
                    container.appendChild(image);

                    /* Description */
                    if (typeof(module.screenshots[index].description) !== 'undefined') {
                        let descripton = document.createElement('div');

                        descripton.classList.add('carousel-caption');
                        descripton.classList.add('d-none');
                        descripton.classList.add('d-md-block');
                        descripton.innerHTML = '<p>' + module.screenshots[index].description + '</p>';

                        container.appendChild(descripton);
                    }

                    screenshots.appendChild(container);
                });
            } else {
                screenshots.innerHTML = '';
            }
        } catch (e) {
            console.error('[ERROR]', 'Can\'t handle Module-Screenshots:', e);
        }

        /* Changelog */
        try {
            if(response.changelog.length > 0) {
                tabs.push('changelog');
                modal.querySelector('.nav-item.changelog-tab').classList.remove('d-none');
                modal.querySelector('div#changelog-pane').innerHTML = response.changelog;
            } else {
                modal.querySelector('div#changelog-pane').innerHTML = '';
            }
        } catch (e) {
            console.error('[ERROR]', 'Can\'t handle Module-Changelog:', e);
        }

        /* Find the first Tab */
        if(tabs.length > 0) {
            let active = tabs[0];

            modal.querySelector('.nav.nav-tabs').classList.remove('d-none');
            modal.querySelector('.nav-item.' + active + '-tab').classList.remove('d-none');
            modal.querySelector('.nav-item.' + active + '-tab .nav-link').classList.add('active');
            modal.querySelector('.nav-item.' + active + '-tab .nav-link').ariaSelected = true;
            modal.querySelector('#' + active + '-pane').classList.add('active');
            modal.querySelector('#' + active + '-pane').classList.add('show');
        } else {
            modal.querySelector('.nav.nav-tabs').classList.add('d-none');
        }

        this.loading(modal, false);
    }

    loading(modal, state) {
        let loading = modal.querySelector('.modal-loading');

        if(loading) {
            loading.dataset.fetching = (state ? 'true' : 'false');
        }
    }

    onShow(event) {
        let modal = event.target;
        modal.classList.add('modal-xl');
        this.loading(modal, true);

        /* Default hide all Tabs */
        [
            'details',
            'screenshots',
            'changelog',
            'features'
        ].forEach((tab) => {
            modal.querySelector('.nav-item.' + tab + '-tab').classList.add('d-none');
            modal.querySelector('#' + tab + '-pane').classList.remove('show');
            modal.querySelector('#' + tab + '-pane').classList.remove('active');
            modal.querySelector('.nav-item.' + tab + '-tab .nav-link').classList.remove('active');
            modal.querySelector('.nav-item.' + tab + '-tab .nav-link').ariaSelected = false;
        });

        new Ajax('/ajax').onSuccess( (response) => {
            switch(response) {
                case 'NO_PERMISSIONS':
                    this.error(modal, 'You have no permissions for this action.'); // @ToDo Language
                break;
                case 'NO_REPOSITORYS':
                    this.error(modal, 'No repositorys.'); // @ToDo Language
                break;
                case 'MODULE_NOT_FOUND':
                    this.error(modal, 'Module not found.'); // @ToDo Language
                break;
                case 'MODULE_EMPTY':
                    this.error(modal, 'Module is empty.'); // @ToDo Language
                break;
                default:
                    this.build(modal, response);
                break;
            }
        }).post({
            'module': event.relatedTarget.dataset.module
        });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.ModuleInfo = new ModuleInfo();
});