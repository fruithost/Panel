/*
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
*/
'use strict';

class Theme {
    constructor() {
        this.icons = {
            light:  'sun-fill',
            dark:   'moon-stars-fill',
            auto:   'circle-half'
        };

        this.createElement();
        this.setTheme(this.getPreferredTheme());

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const storedTheme = this.getStoredTheme();

            if(storedTheme !== 'light' && storedTheme !== 'dark') {
                this.setTheme(this.getPreferredTheme());
            }
        });

        this.showActiveTheme(this.getPreferredTheme());

        document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
            toggle.addEventListener('click', (event) => {
                this.getChange(event.target);
            });
        });
    }

    createElement() {
        let container = document.createElement('div');
        let active = document.createElement('button');
        let list    = document.createElement('ul');

        [
            'theme-switch',
            'dropdown',
            'position-fixed',
            'bottom-0',
            'end-0',
            'mb-3',
            'me-3',
            'bd-mode-toggle'
        ].forEach(className => {
            container.classList.add(className);
        });

        /* Active Button */
        [
            'btn',
            'border-0',
            'py-2',
            'dropdown-toggle',
            'd-flex',
            'align-items-center'
        ].forEach(className => {
            active.classList.add(className);
        });

        active.id               = 'theme-active';
        active.type             = 'button';
        active.ariaExpanded     = 'false';
        active.ariaLabel        = 'Toggle theme (Auto)'; // @ToDo Language
        active.dataset.bsToggle = 'dropdown';

        // Icon
        let icon = document.createElement('i');
        icon.classList.add('bi-' + this.icons.auto);
        icon.classList.add('me-2');
        icon.classList.add('opacity-50');
        icon.classList.add('theme-icon');
        icon.dataset.current = 'auto';
        active.appendChild(icon);

        // Span
        let span = document.createElement('span');
        span.classList.add('visually-hidden');
        span.id         = 'theme-text';
        span.innerText  = 'Toggle theme'; // @ToDo Language
        active.appendChild(span);

        container.appendChild(active);

        // List
        list.classList.add('dropdown-menu');
        list.classList.add('dropdown-menu-end');
        list.classList.add('shadow');
        list.ariaLabelledby = 'theme-text';
        container.appendChild(list);

        let check = document.createElement('i');
        check.classList.add('ms-auto');
        check.classList.add('d-none');

        Object.keys(this.icons).forEach(type => {
            let li          = document.createElement('li');
            let button  = document.createElement('button');
            let image         = document.createElement('i');

            button.type                     = 'button';
            button.dataset.bsThemeValue     = type;
            button.dataset.ariaPressed      = 'false';
            button.classList.add('dropdown-item');
            button.classList.add('d-flex');
            button.classList.add('align-items-center');

            image.classList.add('me-2');
            image.classList.add('opacity-50');
            image.classList.add('theme-icon');
            image.classList.add('bi-' + this.icons[type]);
            image.dataset.name = type;

            button.appendChild(image);
            button.appendChild(document.createTextNode(type));
            button.appendChild(check);
            li.appendChild(button);
            list.appendChild(li);
        });

        document.body.appendChild(container);
    }

    getChange(element) {
        const theme = element.getAttribute('data-bs-theme-value');
        this.setStoredTheme(theme);
        this.setTheme(theme);
        this.showActiveTheme(theme, true);
    }

    getPreferredTheme() {
        const storedTheme = this.getStoredTheme();

        if(storedTheme) {
            return storedTheme;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    getStoredTheme() {
        return localStorage.getItem('theme');
    }

    setStoredTheme(theme) {
        localStorage.setItem('theme', theme);
    }

    setTheme(theme) {
        let type = theme;

        if(theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            type = 'dark';
        }

        document.documentElement.setAttribute('data-bs-theme', theme);
    }

    showActiveTheme(theme, focus) {
        const switcher = document.querySelector('.theme-switch');

        if(!switcher) {
            return;
        }

        const text = switcher.querySelector('#theme-text');
        const icon = switcher.querySelector('#theme-active i');
        const active = switcher.querySelector(`[data-bs-theme-value="${theme}"]`);
        const svg = active.querySelector('i');

        switcher.querySelectorAll('[data-bs-theme-value]').forEach(element => {
            element.classList.remove('active');
            element.setAttribute('aria-pressed', 'false');
        });

        active.classList.add('active');
        active.setAttribute('aria-pressed', 'true');

        if(icon) {
            Object.keys(this.icons).forEach(type => {
                icon.classList.remove('bi-' + this.icons[type]);
            });

            icon.classList.add('bi-' + this.icons[svg.dataset.name]);
        }

        switcher.setAttribute('aria-label', `${text.textContent} (${active.dataset.bsThemeValue})`);

        if(focus) {
            switcher.focus();
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.Theme = new Theme();
});