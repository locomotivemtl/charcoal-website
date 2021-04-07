import { module } from 'modujs';
import { html } from '../utils/environment';

export default class extends module {
    constructor(m) {
        super(m);

        this.events = {
            click: 'toggle'
        }
    }

    init() {

    }

    toggle() {
        html.classList.toggle('has-products-open');
    }
}
