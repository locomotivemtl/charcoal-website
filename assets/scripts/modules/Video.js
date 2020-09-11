import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {

    }

    toggle(e) {
        if(e.way === "enter") {
            this.$('video')[0].play();
        } else {
            this.$('video')[0].pause();
        }
    }
}
