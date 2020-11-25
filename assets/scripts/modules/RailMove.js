export default class RailMove {
    constructor(el, speed, isFixed) {
        this.el = el
        this.isFixed = isFixed;

        this.containerWidth = 0
        this.requestAnimation = null
        this.scrollPosition = -1
        this.translation = 0

        this.grabbed = false
        this.preventClick = false
        this.paused = true

        this.originalVelocity = -speed
        this.velocity = this.originalVelocity
        this.orientation = Math.sign(this.velocity)

        this.lastDelta = null

        this.initializeElements()

    }

    launch() {
        this.initializeEvents();
        this.updateOrientation()
        this.resume()
    }

    initializeElements() {
        this.$railMove = this.el.querySelectorAll('.js-rail-group-container')
        this.$railMoveChildren = this.el.querySelectorAll('.js-rail-item')
        this.getBCR()
    }

    initializeEvents() {
        this.update()
    }

    setContainerWidth(width) {
        this.containerWidth = width
    }

    getBCR() {
        this.railMoveBCR = this.$railMove[0].getBoundingClientRect()
    }

    updateScroll(instance) {
        if(this.paused) return

        if (document.documentElement.classList.contains('has-scroll-smooth') && !this.isFixed) {
            let scrollY = Math.round(instance.scroll.y) ? Math.round(instance.scroll.y) : 0;

            let newScrollPosition = scrollY - this.scrollPosition;
            this.scrollPosition = scrollY;

            if(newScrollPosition != 0) {
                let newVelocity = this.originalVelocity * newScrollPosition;
                this.velocity = newVelocity

                let newOrientation = Math.sign(newVelocity)
                if (newOrientation != this.orientation) {
                    this.orientation = Math.sign(newVelocity)

                    this.updateOrientation()
                }
            }
        }
    }

    pause() {
        this.paused = true
        this.el.classList.add('is-paused')

        if(this.requestAnimation !== undefined) {
            cancelAnimationFrame(this.requestAnimation)
        }
    }

    resume() {
        this.el.classList.remove('is-paused')
        this.paused = false
        this.update();
    }

    update(){
        if(!this.grabbed) {
            if((this.translation > this.railMoveBCR.width / 2) || (this.translation < -this.railMoveBCR.width / 2)) {
                this.translation = 0
            } else {
                this.translation = this.translation + this.velocity
            }
        }

        let translation;
        if(this.translation > 0) {
            translation = -this.containerWidth + (this.translation % this.containerWidth)
        } else {
            translation = this.translation % this.containerWidth
        }

        TweenMax.set(this.$railMoveChildren, { x: Math.round(translation), force3D: true })

        if(!this.paused)
            this.requestAnimation = requestAnimationFrame(() => this.update())
    }

    updateOrientation() {
        if(this.orientation > 0) {
            this.el.classList.remove('is-going-left')
            this.el.classList.add('is-going-right')
        } else if (this.orientation < 0) {
            this.el.classList.remove('is-going-right')
            this.el.classList.add('is-going-left')
        }
    }

    destroy() {
        if(this.requestAnimation !== undefined) {
            cancelAnimationFrame(this.requestAnimation)
        }
        
        TweenMax.set(this.$railMove, { x: 0 })
        this.translation = 0;
    }
}