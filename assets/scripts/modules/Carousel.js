import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.carousel = new Swiper(this.el, {
            loop: false,
            grabCursor: true,
            slidesPerView: 1.5,
            spaceBetween: 20,
            slideToClickedSlide: true,
        })
    }

    destroy() {
        this.carousel.destroy(true,true)
    }
}
