import { module } from 'modujs';
import modularLoad from 'modularload';
import { html } from '../utils/environment.js';

class superLoad extends modularLoad {
    refresh(href, transition) {
        this.reset()
        this.transition = transition
        this.isUrl = void 0

        this.setOptions((href || window.location.href), false)
    }
}

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.load = new superLoad({
            enterDelay: 900,
            transitions: {
                customTransition: {}
            }
        });

        this.load.on('loading', (transiton, oldContainer) => {
            html.classList.remove('has-products-open');
            html.classList.remove('has-dom-ready');
            html.classList.remove('has-mobile-menu-open');
        });

        this.load.on('loaded', (transition, oldContainer, newContainer) => {
            this.call('destroy', oldContainer, 'app');
            this.call('update', newContainer, 'app');

            setTimeout(() => {
                html.classList.add('has-dom-ready');
            }, 800);
        });
    }

    goTo(options) {
        let { url, transition } = options
        this.load.goTo(url, transition)
    }

    refresh(options) {
        let { url, transition } = options
        this.load.refresh(url, transition)
    }
}
