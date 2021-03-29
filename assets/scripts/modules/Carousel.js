import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.carouselMain = new Swiper(this.$('main')[0], {
            grabCursor: true,
            shortSwipes: false,
            longSwipesRatio: 0.1,
            longSwipesMs: 50,
            parallax: true,
            navigation: {
                nextEl: this.$('next')[0],
                prevEl: this.$('prev')[0],
            }
        })

        this.carouselContent = new Swiper(this.$('content')[0], {
            allowTouchMove: false,
        });

        this.carouselVisual = new Swiper(this.$('visuals')[0], {
            grabCursor: true,
            shortSwipes: false,
            longSwipesRatio: 0.1,
            longSwipesMs: 50,
            parallax: true
        });

        this.carouselMain.on('slideChange', () => {
            this.carouselContent.slideTo(this.carouselMain.realIndex);
        });

        this.carouselMain.controller.control = this.carouselVisual;
        this.carouselVisual.controller.control = this.carouselMain;
    }

    destroy() {
        this.carousel.destroy(true, true)
    }
}
