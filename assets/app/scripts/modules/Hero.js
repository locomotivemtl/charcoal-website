import { module } from 'modujs';
import { html } from '../utils/environment'

// Needs to be update manually. Use dat.gui for easier anim manipulation in Lottie.js
const SEGMENT_FRAMES = [
    [0, 60],        // Shape : Diamond
    [144, 240],     // Shape : Rectangle rotated
    [320, 415],     // Shape : Triangle rotated
    [495, 598],     // Shape : Parallelogram
    [677, 719]      // Shape : Diamond
]

export default class extends module {
    constructor(m) {
        super(m);

        this.currentFrame               = 0
        this.hasScrolled                = false
        this.hasDeviceVisible           = false
        this.isScrollingAnimCompleted   = false
        this.darkUI                     = true
    }

    init() {
        if(window.isMobile) {
            // this.showDevice()
            return
        }

        this.initHero()

        setTimeout(() => {
            this.launch()
        }, 2000);
    }

    launch() {
        this.call('play', null, 'Lottie', 'charcoal-intro')
    }

    initHero() {
        html.classList.add('has-hero-running')

        this.maskTL = gsap.timeline({
            defaults: {
                ease: "none",
                duration: 1
            },
            onComplete: () => {

            }
        })
        this.maskTL.addLabel('start')
        this.maskTL.fromTo(this.$('mask')[0], { scale: 1 }, { scale: 5.5 }, 'start')
        this.maskTL.fromTo(this.$('logo')[0], { scale: 0.15 }, { scale: 1 }, 'start')
        this.maskTL.pause()
    }

    /**
     * Manage the progress and other anim variables when user scrolls
     *
     * @param {scroll obj} (see Scroll.js)
     */
    onScroll(param) {
        if(window.isMobile) return

        let limit = param[0].section.limit.y - (window.innerHeight * 1.5)
        let scroll = param[1].scroll.y

        let progress = Math.min( (scroll / limit).toFixed(3), 1 )

        if (progress >= 1)
            progress = 1
        else if(progress <= 0)
            progress = 0

        if(!this.hasScrolled && scroll > 0) { //check if the user has scrolled and if the scroll
            //launch segment detection
            this.checkSegment()
            //set hasScrolled
            this.hasScrolled = true
        } else if(this.hasScrolled && scroll == 0) { //check if we are BACK to the top of the page
            //clear segment detection
            if(this.rafSegment) cancelAnimationFrame(this.rafSegment)
            //play the animation
            this.call('play', null, 'Lottie')
            //set hasScrolled
            this.hasScrolled = false
        }

        // Manage UI light/dark classes
        if(progress >= 0.3) {
            if(this.darkUI) {
                html.classList.remove('has-hero-running')
                this.darkUI = false
            }
        } else {
            if(!this.darkUI) {
                html.classList.add('has-hero-running')
                this.darkUI = true
            }
        }

        // Manage the phone
        if(progress >= 0.5) {
            if(!this.hasDeviceVisible) {
                if(!this.isScrollingAnimCompleted) {
                    this.showDevice()
                    this.isScrollingAnimCompleted = true
                }
            }
        } else {
            if(this.hasDeviceVisible) {
                if(this.isScrollingAnimCompleted) {
                    this.hideDevice()
                    this.isScrollingAnimCompleted = false
                }
            }
        }

        //update progress of UI
        this.maskTL.progress(progress)
    }

    /**
     * Function that check current Lottie frame and sees if it matches the segment in a raf
     *
     * @param {void}
     */
    checkSegment() {
        this.rafSegment = requestAnimationFrame(() => this.checkSegment())
        this.call('getCurrentFrame', {name: 'Hero', id: 'main'}, 'Lottie')

        // 🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠🧠
        let currentSegment = SEGMENT_FRAMES.find((segment) => {
            return this.currentFrame > segment[0] && this.currentFrame < segment[1]
        })

        if(currentSegment) {
            this.call('pause', null, 'Lottie')
            if(this.rafSegment) cancelAnimationFrame(this.rafSegment)
        }
    }

    /**
     * Show device
     *
     * @param
     */
    showDevice() {
        //Show the device and update variable with callback
        let slideUpCallback = () => {
            this.hasDeviceVisible = true
        }

        this.call('slideUp', { callback: slideUpCallback }, 'DeviceManager', 'hero')
    }

    /**
     * Hide device
     *
     * @param
     */
    hideDevice() {
        //Hide the device and update variable with callback
        let slideOutCallback = () => {
            this.hasDeviceVisible = false
        }

        this.call('slideOut', { callback: slideOutCallback }, 'DeviceManager', 'hero')
    }

/**
     * Toggle (inview or not)
     *
     * @param
     */
    toggle(e) {
        this.call('toggle', e, 'DeviceManager');

        if (e.way === "enter") {
            html.classList.add('has-hero-inview')
        } else {
            html.classList.remove('has-hero-inview')
        }
    }

    /**
     * you know the drill
     *
     * @param
     */
    destroy() {
        super.destroy()

        if(this.rafSegment) cancelAnimationFrame(this.rafSegment)
    }
}
