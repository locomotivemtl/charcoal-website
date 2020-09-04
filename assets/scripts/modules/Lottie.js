import { module } from 'modujs';
import lottieLight from 'lottie-web/build/player/lottie_light';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {
        this.fileName = this.getData('file-name')
        this.filePath = this.getData('file-path')

        this.initLottie()
    }

    /**
     * Init lottie
     *
     * @param
     */
    initLottie() {
        this.animation = lottieLight.loadAnimation({
            container: this.el,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: `${this.filePath}/${this.fileName}.json`,
            rendererSettings: {
                preserveAspectRatio: 'xMidYMid slice'
            }
        });
    }

    /**
     * Destroy lottie
     *
     * @param
     */
    destroy() {
        super.destroy()

        if(this.animation && this.animation.destroy) this.animation.destroy()
    }
}
