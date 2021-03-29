import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.carouselVisual = new Swiper(this.$('carousel-visual')[0], {
            grabCursor: true,
            speed: 600,
        })

        this.carouselInfo = new Swiper(this.$('carousel-info')[0], {
            grabCursor: true,
            speed: 600,
            shortSwipes: false,
            longSwipesRatio: 0.1,
            longSwipesMs: 50,
            allowTouchMove: false,
            navigation: {
                nextEl: this.$('next')[0],
                prevEl: this.$('prev')[0],
            },
        })

        this.carouselInfo.controller.control = this.carouselVisual;
        this.carouselVisual.controller.control = this.carouselInfo;

        // this.carouselInfo.on('slideChange', () => {
        //     this.setURL();
        // });

        // this.setURL();
    }

    setURL() {
        let url = this.carouselInfo.slides[this.carouselInfo.activeIndex].dataset.projectUrl
        url ??= false;

        if (url.length > 0) {
            this.$('button')[0].href = url
        }
    }

    destroy() {
        this.carouselVisual.destroy(true, true)
        this.carouselInfo.destroy(true, true)
    }
}
