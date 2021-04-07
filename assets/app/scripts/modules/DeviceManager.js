import { module } from 'modujs';
import Smartphone from './Smartphone';
import Laptop from './Laptop';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {

        if(window.isMobile) {
            this.deviceInstance = new Smartphone(this.el);
        } else {
            this.deviceInstance = new Laptop(this.el);
        }
    }

    toggle(e) {
        this.deviceInstance.toggle(e);
    }

    slideUp(e) {
        this.deviceInstance.slideUp(e);
    }

    slideOut(e) {
        this.deviceInstance.slideOut(e);
    }
}
