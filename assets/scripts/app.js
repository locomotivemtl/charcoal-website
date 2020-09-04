import modular from 'modujs';
import * as modules from './modules';
import globals from './globals';
import { html } from './utils/environment';

const app = new modular({
    modules: modules
});

window.onload = (event) => {
    const $style = document.getElementById("stylesheet");

    if ($style.isLoaded) {
        init();
    } else {
        $style.addEventListener('load', (event) => {
            init();
        });
    }
};

function init() {
    app.init(app);
    globals();

    setTimeout(() => {
        html.classList.add('is-first-load');
        html.classList.add('is-loaded');
        html.classList.add('is-ready');
        html.classList.remove('is-loading');
    }, 600);
}

