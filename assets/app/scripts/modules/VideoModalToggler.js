import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);

        this.events = {
            click: {
                toggler: 'videoClick'
            }
        }
    }

    init() {

    }

    videoClick(e) {
        e.preventDefault()

        const el = e.curTarget
        const id = this.getData('id', el)
        const host = this.getData('host', el)

        this.call('openVideo', { id, host }, 'VideoModal')
    }
}
