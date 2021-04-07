import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.carousel = new Swiper(this.el, {
            speed: 600,
            loopedSlides: 2,
            shortSwipes: false,
            longSwipesRatio: 0.1,
            longSwipesMs: 50,
            allowTouchMove: false,
            navigation: {
                nextEl: this.$('next')[0],
                prevEl: this.$('prev')[0],
            }
        })
    }

    destroy() {
        this.carousel.destroy(true,true)
    }
}
