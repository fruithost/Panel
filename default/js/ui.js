(() => {
    function show(event) {
        [].map.call(document.querySelectorAll('.bi[data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"] .bi'), function (icon) {
            icon.classList.remove('bi-caret-up-square-fill');
            icon.classList.remove('bi-caret-down-square-fill');
            icon.classList.add('bi-caret-up-square-fill');
        });
    };

    function hide(event) {
        [].map.call(document.querySelectorAll('.bi[data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"] .bi'), function (icon) {
            icon.classList.remove('bi-caret-up-square-fill');
            icon.classList.remove('bi-caret-down-square-fill');
            icon.classList.add('bi-caret-down-square-fill');
        });
    };

    [].map.call(document.querySelectorAll('.collapse'), function (collapse) {
        collapse.addEventListener('show.bs.collapse', show, false);
        collapse.addEventListener('shown.bs.collapse', show, false);
        collapse.addEventListener('hide.bs.collapse', hide, false);
        collapse.addEventListener('hidden.bs.collapse', hide, false);
    });

    [].map.call(document.querySelectorAll('[data-bs-toggle="collapse"] a[href]'), function (collapse) {
        collapse.addEventListener('click', (event) => {
            window.location.href = event.target.href;
        }, false);
    });


    let sidebar = document.querySelector('.sidebar');

    window.addEventListener('resize', (event) => {
        if (window.getComputedStyle(sidebar, null).display !== 'block') {
            sidebar.classList.remove('show');
        }
    }, true);

    document.querySelector('[data-bs-toggle="sidebar"]').addEventListener('click', (event) => {
        if (window.getComputedStyle(sidebar, null).display !== 'block') {
            sidebar.classList.add('show');
        } else {
            sidebar.classList.remove('show');
        }
    });

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"], [data-bs-toggle="hover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl, {
        trigger: 'hover',
        html: true
    }))

    function serialize(form) {
        var obj = {};
        var formData = new FormData(form);
        for (var key of formData.keys()) {
            obj[key] = formData.get(key);
        }
        return obj;
    };

    function ajaxSubmit(event) {
        event.preventDefault();

        try {
            let form = this;

            // @ToDo add onError?

            new Ajax(form.action).onSuccess(function (response) {
                if (response.toLowerCase() === 'true' || response.toLowerCase() === '1') {
                    window.location.reload();
                    return;
                } else if (response.toLowerCase() == 'close') {
                    let node = form.parentNode.closest('.modal');
                    node.classList.remove('fade');
                    var modal = bootstrap.Modal.getInstance(node);
                    modal.hide();
                    event.stopPropagation();
                    return;
                } else if (response.toLowerCase() === 'false') {
                    response = 'An unknown error has occurred.';
                }

                let content = form.querySelector('.modal-body');
                let alert = content.querySelector('.alert');

                if (alert) {
                    content.removeChild(alert);
                }

                alert = document.createElement('div');
                alert.classList.add('alert');
                alert.classList.add('alert-danger');
                alert.setAttribute('role', 'alert');
                alert.innerHTML = response;
                content.prepend(alert);
            }).post(serialize(form));
        } catch (e) {

        }

        return false;
    };

    [].map.call(document.querySelectorAll('.ajax'), function (form) {
        /*form.addEventListener('click', function(event) {
            ajaxSubmit.apply(form, [ event ]);
        });*/

        form.addEventListener('submit', function (event) {
            ajaxSubmit.apply(form, [event]);
        });
    });

    [].map.call(document.querySelectorAll('[data-loading]'), function (element) {
        element.addEventListener('click', function (event) {
            let element = event.target;

            element.innerHTML = '';

            let spinner = document.createElement('span');
            spinner.classList.add('spinner-border');
            spinner.classList.add('spinner-border-sm');
            element.append(spinner);

            let text = document.createElement('span');
            text.innerText = ' ' + element.dataset.loading;
            element.append(text);

            let dots = document.createElement('span');
            dots.dataset.dots = 'true';
            element.append(dots);
        }, false);
    });

    document.querySelector('div#module_info').addEventListener('show.bs.modal', (event) => {
        let modal = event.target;
        modal.classList.add('modal-xl');
        modal.querySelector('.modal-loading').dataset.fetching = true;

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

        new Ajax('/ajax').onSuccess(function (response) {
            switch (response) {
                case 'NO_PERMISSIONS':

                    break;
                case 'NO_REPOSITORYS':

                    break;
                case 'MODULE_NOT_FOUND':

                    break;
                case 'MODULE_EMPTY':

                    break;
                default:

                    break;
            }

            let tabs = [];
            let module = response.info;

            if (typeof (response.info) == 'undefined') {
                return;
            }

            /* Header */
            try {
                modal.querySelector('.modal-title').innerText = module.name;
                modal.querySelector('.module-icon').innerHTML = module.icon;
                modal.querySelector('.module-name').innerText = module.name;
                modal.querySelector('.module-version').innerText = module.version;
                modal.querySelector('.module-description').innerText = module.description;

                modal.querySelector('.module-author').innerHTML = '<a href="mailto:' + module.author.email + '" class="text-decoration-none" target="_blank">' + module.author.name + '</a>';
                modal.querySelector('.module-url').innerHTML = '<a href="' + module.author.url + '" class="text-decoration-none" target="_blank">' + module.author.url + '</a>';
            } catch (e) {
                console.error('Cant handle Module-Infos:', e);
            }

            /* Readme */
            try {
                if (response.readme.length > 0) {
                    tabs.push('details');
                    modal.querySelector('.nav-item.details-tab').classList.remove('d-none');
                    modal.querySelector('div#details-pane').innerHTML = response.readme;
                } else {
                    modal.querySelector('div#details-pane').innerHTML = '';
                }
            } catch (e) {
                console.error('Cant handle Module-Readme:', e);
            }

            /* Screenshots */
            try {
                if (response.screenshots.length > 0) {
                    tabs.push('screenshots');
                    let screenshots = modal.querySelector('div#screenshotHolder div.carousel-inner');
                    modal.querySelector('.nav-item.screenshots-tab').classList.remove('d-none');
                    screenshots.innerHTML = '';

                    response.screenshots.forEach((entry, index) => {
                        let container = document.createElement('div');
                        container.classList.add('carousel-item');
                        if (index == 0) {
                            container.classList.add('active');
                        }

                        /* Image */
                        let image = new Image();
                        image.src = entry;
                        image.classList.add('d-block');
                        image.classList.add('w-100');
                        container.appendChild(image);

                        /* Description */
                        if (typeof (module.screenshots[index].description) !== 'undefined') {
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
                console.error('Cant handle Module-Screenshots:', e);
            }

            /* Changelog */
            try {
                if (response.changelog.length > 0) {
                    tabs.push('changelog');
                    modal.querySelector('.nav-item.changelog-tab').classList.remove('d-none');
                    modal.querySelector('div#changelog-pane').innerHTML = response.changelog;
                } else {
                    modal.querySelector('div#changelog-pane').innerHTML = '';
                }
            } catch (e) {
                console.error('Cant handle Module-Changelog:', e);
            }

            /* Find the first Tab */
            if (tabs.length > 0) {
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

            modal.querySelector('.modal-loading').dataset.fetching = false;
        }).post({
            'module': event.relatedTarget.dataset.module
        });
    });

    [].map.call(document.querySelectorAll('[data-confirm]'), function (element) {
        element.addEventListener('mousedown', function (event) {

            let target = event.target;
            let el = document.querySelector('#confirmation');
            const popup = new bootstrap.Modal(el);
            let prevent = true;
            let header = popup._element.querySelector('.modal-header');

            let a = document.createElement('p');
            a.classList.add('m-0');
            a.classList.add('text-center');
            a.classList.add('p-3');
            a.innerHTML = target.dataset.confirm;

            let b = document.createElement('p');
            b.classList.add('modal-body');
            b.classList.add('d-none');
            b.innerHTML = target.dataset.confirm;

            [].map.call(header.parentNode.querySelectorAll('p'), function (e) {
                e.parentNode.removeChild(e);
            });

            popup._element.querySelector('.modal-footer').classList.add('text-center');


            header.parentNode.insertBefore(a, header.nextSibling);
            header.parentNode.insertBefore(b, header.nextSibling);

            popup.show();

            let _watcher = setInterval(function () {
                var res = el.querySelector('.alert');

                if (res == null || typeof (res) === 'undefined') {
                    return;
                }
                var state = res.innerText;

                if (state === 'CONFIRMED') {
                    clearInterval(_watcher);

                    if (target.tagName === 'A') {
                        window.location.href = target.href;
                    } else {
                        target.click();
                    }
                }
            }, 500);

            el.addEventListener('hide.bs.modal', function (event) {
                clearInterval(_watcher);
            });

            if (prevent) {
                event.preventDefault();
                return false;
            }
        });
    });


    window.showing.forEach((modal) => {
        new bootstrap.Modal('#' + modal, {}).show();
    });
})();


/*

	$('.sortable').sortable({
		appendTo:				'parent',
		cursor:					'move',
		handle:					'.moveable',
		dropOnEmpty:			false,
		forceHelperSize:		false,
		forcePlaceholderSize:	false,
		helper:					'original',
		items:					'> li',
		placeholder: {
			element: function(currentItem) {
				return $('<div class="sortable-placeholder"><div class="card m-2">Drop here</div></div>')[0];
			},
			update: function(container, p) {
				return;
			}
		},
		scroll:					false,
		beforeStop:				function onBeforeClose(event, ui) {
			$('body').removeClass('sortable');
		},
		start:					function onStart(event, ui) {
			$('body').addClass('sortable');
			ui.placeholder.addClass('col-6');
		}
	});
}(jQuery));*/