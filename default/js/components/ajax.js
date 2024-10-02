/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */
'use strict';

class AjaxComponent {
    constructor() {
        [].map.call(document.querySelectorAll('.ajax'),  (form) => {
            /*
                form.addEventListener('click', (event) => {
                    this.submit.apply(form, [ event ]);
                });
            */

            form.addEventListener('submit', (event) => {
                this.submit.apply(form, [ event ]);
            });
        });
    }
    serialize(form) {
        let result = {};
        let data = new FormData(form);

        for(let key of data.keys()) {
            result[key] = data.get(key);
        }

        return result;
    }

    submit(event) {
        event.preventDefault();

        try {
            let form = this;
            // @ToDo add onError?

            new Ajax(form.action).onSuccess(function (response) {
                if(response.toLowerCase() === 'true' || response.toLowerCase() === '1') {
                    window.location.reload();
                    return;

                } else if(response.toLowerCase() == 'close') {
                    let node = form.parentNode.closest('.modal');
                    node.classList.remove('fade');
                    let modal = bootstrap.Modal.getInstance(node);
                    modal.hide();
                    event.stopPropagation();
                    return;

                } else if(response.toLowerCase() === 'false') {
                    response = 'An unknown error has occurred.'; // @ToDo Language
                }

                let content = form.querySelector('.modal-body');
                let alert   = content.querySelector('.alert');

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
            /* Do Nothing */
        }

        return false;
    }
}

window.addEventListener('DOMContentLoaded', () => {
    window.AjaxComponent = new AjaxComponent();
});