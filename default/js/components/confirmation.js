/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */

(() => {
    window.Confirm = function Confirm(element) {
        element.addEventListener('mousedown', function (event) {
            let target          = event.target;
            let el     = document.querySelector('#confirmation');
            const popup  = new bootstrap.Modal(el);
            let prevent= true;
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

                if(res == null || typeof (res) === 'undefined') {
                    return;
                }

                var state = res.innerText;

                if(state === 'CONFIRMED') {
                    clearInterval(_watcher);

                    if(target.tagName === 'A') {
                        window.location.href = target.href;
                    } else {
                        target.click();
                    }
                }

            }, 500);

            el.addEventListener('hide.bs.modal', function (event) {
                clearInterval(_watcher);
            });

            if(prevent) {
                event.preventDefault();
                return false;
            }
        });
    };

    [].map.call(document.querySelectorAll('[data-confirm]'), Confirm);
})();