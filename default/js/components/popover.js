/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */
'use strict';

class Popover {
    constructor() {
        document.querySelectorAll('[data-bs-toggle="popover"], [data-bs-toggle="hover"]').forEach((element) => {
            new bootstrap.Popover(element, {
                trigger:    'hover',
                html:       true
            });
        });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.Popover = new Popover();
});