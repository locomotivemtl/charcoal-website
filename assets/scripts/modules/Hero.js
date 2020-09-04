import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {

    }

    update(param) {
        let limit = param[0].section.limit.y - (window.innerHeight * 1.5)
        let scroll = param[1].scroll.y

        let progress = scroll / limit

        if (progress >= 1)
            progress = 1
        else if(progress <= 0)
            progress = 0

        gsap.set(this.$('progress')[0], { scaleX: (progress) })
    }

    setBounds() {
        // this.
    }

    getBounds() {
        return this.el.getBoundingClientRect()
    }
}
