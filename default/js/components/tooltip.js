/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */
'use strict';

class Tooltip {
    constructor() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
            new bootstrap.Tooltip(element)
        });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.Tooltip = new Tooltip();
});