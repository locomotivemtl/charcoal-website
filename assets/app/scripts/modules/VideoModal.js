import { module } from 'modujs'

export default class extends module {
    constructor(m) {
        super(m)

        this.events = {
            click: {
                close: "close"
            }
        }

        this.inner = this.$('inner')[0]
    }

    openVideo(e) {
        if(this.emptyTimeout) clearTimeout(this.emptyTimeout)

        this.appendDelay = setTimeout(() => {
            switch (e.host) {
                case 'youtube':
                    this.inner.innerHTML = `<iframe src="https://www.youtube.com/embed/${e.id}?&autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`
                    break
                case 'vimeo':
                    this.inner.innerHTML = `<iframe src="https://player.vimeo.com/video/${e.id}?autoplay=1&loop=1&autopause=0" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`
                    break
                default:
                console.log('default')
                    break
            }
        }, 500)
        this.el.classList.add('is-active')
    }

    close() {
        clearTimeout(this.appendDelay)

        this.el.classList.remove('is-active')
        this.emptyTimeout = setTimeout(() => {
            this.inner.innerHTML=''
        }, 250)
    }
}
