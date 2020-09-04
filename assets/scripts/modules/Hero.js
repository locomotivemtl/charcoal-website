import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.maskTL = gsap.fromTo(this.$('mask')[0], { scale: 1 }, { scale: 3.5 })
        this.maskTL.pause()
    }

    update(param) {
        let limit = param[0].section.limit.y - (window.innerHeight * 1.5)
        let scroll = param[1].scroll.y

        let progress = Math.min( (scroll / limit).toFixed(3), 1 )

        if (progress >= 1)
            progress = 1
        else if(progress <= 0)
            progress = 0

        gsap.set(this.$('progress')[0], { scaleX: (progress) })
        this.maskTL.progress(progress)
    }

    setBounds() {
        // this.
    }

    getBounds() {
        return this.el.getBoundingClientRect()
    }
}
