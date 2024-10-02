/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */
'use strict';

class Modal {
    constructor() {
        if(typeof(window.showing) === 'undefined') {
            console.warn('[WARNING]', 'window.showing', 'is Empty!');
            return;
        }

        window.showing.forEach((modal) => {
            let instance = new bootstrap.Modal('#' + modal, {});

            if(instance) {
                instance.show();
            }
        });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.Modal = new Modal();
});