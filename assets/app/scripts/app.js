import modular from 'modujs';
import * as modules from './modules';
import globals from './globals';
import { html } from './utils/environment';

const app = new modular({
    modules: modules
});

window.onload = (event) => {
    const $style = document.getElementById('css-app-main');

    if ($style) {
        if ($style.isLoaded) {
            init();
        } else {
            $style.addEventListener('load', (event) => {
                init();
            });
        }
    } else {
        console.warn('The "css-app-main" stylesheet not found');
    }
};

function init() {
    globals();
    app.init(app);

    setTimeout(() => {
        html.classList.add('is-first-load');
    }, 200)

    setTimeout(() => {
        html.classList.add('is-loaded');
        html.classList.add('is-ready');
        html.classList.remove('is-loading');

        setTimeout(() => {
            html.classList.add('has-dom-ready');
        }, 600);
    }, 1000);
}

