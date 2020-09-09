import { module } from 'modujs';
import lottieLight from 'lottie-web/build/player/lottie_light';

export default class extends module {
    constructor(m) {
        super(m);

        this.events = {
            click: 'getCurrentFrame'
        }
    }

    init() {
        this.fileName = this.getData('file-name')
        this.filePath = this.getData('file-path')
        this.autoplay = this.getData('autoplay') ? this.getData('autoplay') : false

        this.initLottie()
    }

    /**
     * Init lottie
     *
     * @param
     */
    initLottie() {
        lottieLight.setQuality('low')
        this.animation = lottieLight.loadAnimation({
            container: this.el,
            renderer: 'svg',
            loop: true,
            autoplay: this.autoplay,
            path: `${this.filePath}/${this.fileName}.json`,
            rendererSettings: {
                preserveAspectRatio: 'xMidYMid slice'
            }
        });

        // setTimeout(() => {
        //     this.animation.pause()
        //     this.initGui()
        // }, 500);
    }

    /**
     * Pause
     *
     * @param
     */
    pause() {
        if(this.animation && this.animation.pause) this.animation.pause()
    }

    /**
     * Play
     *
     * @param
     */
    play() {
        if(this.animation && this.animation.play) this.animation.play()
    }

    /**
     * Get the current frame
     * @TODO better condition
     * @param
     */
    getCurrentFrame(module) {
        if(this.animation) {
            let moduleName = module.name
            let moduleId = module.id

            if(this.modules && this.modules[moduleName]) {
                this.modules.Hero[moduleId].currentFrame = this.animation.currentFrame
            }

            return this.animation.currentFrame
        } else {
            return 0
        }
    }

    /**
     * Go to and stop
     *
     * @param
     */
    goToAndStop(frame) {
        if(this.animation && this.animation.goToAndStop) this.animation.goToAndStop(frame, true)
    }

    /**
     * Init GUI if needed
     *
     * @param
     */
    initGui() {
        this.gui = new dat.GUI();

        let frames = this.gui.addFolder('Frames')
        let currentFrame = frames.add(this, 'currentFrame', 0, this.animation.totalFrames);

        currentFrame.onChange((value) => {
            this.goToAndStop(value)
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
