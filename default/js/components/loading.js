/**
 * fruithost | OpenSource Hosting
 *
 * @author Adrian Preuß
 * @version 1.0.0
 * @license MIT
 */

(() => {
    window.Loading = function Loading(element) {
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
    };

    [].map.call(document.querySelectorAll('[data-loading]'), Loading);
})();