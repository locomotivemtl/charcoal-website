import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.carousel = new Swiper(this.el, {
            loop: false,
            grabCursor: true,
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
            shortSwipes: false,
            longSwipesRatio: 0.1,
            longSwipesMs: 50,
            breakpoints: {
                700: {
                    slidesPerView: 1.5
                }
            },
        })
    }

    destroy() {
        this.carousel.destroy(true,true)
    }
}
