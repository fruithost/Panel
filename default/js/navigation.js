/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */
'use strict';

class Navigation {
    constructor() {
        let sidebar = document.querySelector('.sidebar');

        [].map.call(document.querySelectorAll('.collapse'),  (collapse) => {
            collapse.addEventListener('show.bs.collapse', this.show, false);
            collapse.addEventListener('shown.bs.collapse', this.show, false);
            collapse.addEventListener('hide.bs.collapse', this.hide, false);
            collapse.addEventListener('hidden.bs.collapse', this.hide, false);
        });

        [].map.call(document.querySelectorAll('[data-bs-toggle="collapse"] a[href]'), (collapse) => {
            collapse.addEventListener('click', (event) => {
                window.location.href = event.target.href;
            }, false);
        });

        window.addEventListener('resize', (event) => {
            if(window.getComputedStyle(sidebar, null).display !== 'block') {
                sidebar.classList.remove('show');
            }
        }, true);

        document.querySelector('[data-bs-toggle="sidebar"]').addEventListener('click', (event) => {
            if(window.getComputedStyle(sidebar, null).display !== 'block') {
                sidebar.classList.add('show');
            } else {
                sidebar.classList.remove('show');
            }
        });
    }

    show(event) {
        [].map.call(document.querySelectorAll('.bi[data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"] .bi'), function (icon) {
            icon.classList.remove('bi-caret-up-square-fill');
            icon.classList.remove('bi-caret-down-square-fill');
            icon.classList.add('bi-caret-up-square-fill');
        });
    }

    hide(event) {
        [].map.call(document.querySelectorAll('.bi[data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"], [data-bs-toggle="collapse"][data-bs-target="#' + event.target.id + '"] .bi'), function (icon) {
            icon.classList.remove('bi-caret-up-square-fill');
            icon.classList.remove('bi-caret-down-square-fill');
            icon.classList.add('bi-caret-down-square-fill');
        });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.Theme = new Navigation();
});