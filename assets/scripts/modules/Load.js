import { module } from 'modujs';
import modularLoad from 'modularload';
import { html } from '../utils/environment.js';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        const load = new modularLoad({
            enterDelay: 900,
            transitions: {
                customTransition: {}
            }
        });

        load.on('loading', (transiton, oldContainer) => {
            html.classList.remove('has-products-open');
            html.classList.remove('has-dom-ready');
            html.classList.remove('has-mobile-menu-open');

        });

        load.on('loaded', (transition, oldContainer, newContainer) => {
            this.call('destroy', oldContainer, 'app');
            this.call('update', newContainer, 'app');

            setTimeout(() => {
                html.classList.add('has-dom-ready');
            },800);
        });
    }
}
