import { module } from 'modujs';
import { shuffle } from '../utils/array';
import RailMove from './RailMove'
import { queryClosestParent } from '../utils/html'

export default class extends module {
    constructor(m) {
        super(m);

        this.speed = this.el.getAttribute('data-rail-speed') || 1;
        this.isFixed = (typeof this.el.getAttribute('data-fixed') === 'string');
        this.shuffle = (typeof this.el.getAttribute('data-shuffle') === 'string');
        this.paused = (typeof this.el.getAttribute('data-paused') === 'string');
        this.isMobile = (typeof this.el.getAttribute('data-desktop') === 'string') ? false : true
        this.initialHTML = this.el.outerHTML;

        this.launched = false
        this.active = false
    }

    init() {
        if(window.isMobile && !this.isMobile) {
            return;
        }

        this.setClasses()
        this.createTrack()
        this.fillScreen()

        this.groupTracks()
        this.duplicateGroup()
        this.wrapGroups()

        this.railMove = new RailMove(this.el, this.speed, this.isFixed)
        this.railMove.setContainerWidth(this.trackGroupBCR.width)

        if(this.paused) this.railMove.pause()
    }

    setClasses() {
        this.el.classList.add('c-rail_wrapper')
        this.children = Array.from(this.el.children)

        for(let child of this.children) {
            child.classList.add('c-rail_item');
            child.classList.add('js-rail-item');
        }

        if(this.shuffle != undefined) {
            let shuffled = shuffle(this.children)
            for(let el of shuffled) {
                this.el.appendChild(el)
            }
        }
    }

    createTrack() {
        this.track = document.createElement('div')
        this.track.classList.add('c-rail_track')
        this.el.appendChild(this.track)

        this.tracks = []
        this.tracks.push(this.track)

        for(let child of this.children) {
            this.track.appendChild(child)
        }
    }

    fillScreen() {
        let ratio = window.innerWidth / this.track.getBoundingClientRect().width

        if(ratio === Infinity) {
            throw new Error('Ratio is Infinity');
        }

        for (var i = 0; i < ratio; i++) {
            let track = this.track.cloneNode(true)
            this.tracks.push(track)
            this.el.appendChild(track)
        }
    }

    groupTracks() {
        this.trackGroup = document.createElement('div')
        this.trackGroup.classList.add('c-rail_track-group')

        this.el.appendChild(this.trackGroup)

        for(let track of this.tracks)
            this.trackGroup.appendChild(track)

        this.trackGroupBCR = this.trackGroup.getBoundingClientRect()
    }

    duplicateGroup() {
        this.trackGroupClone = this.trackGroup.cloneNode(true)
        this.el.appendChild(this.trackGroupClone)
    }

    wrapGroups() {
        this.groupsWrap = document.createElement('div')
        this.groupsWrap.classList.add('js-rail-group-container')
        this.groupsWrap.classList.add('c-rail_group-container')
        this.el.append(this.groupsWrap)

        for(let group of [this.trackGroup, this.trackGroupClone])
            this.groupsWrap.append(group)
    }

    launch() {
        this.launched = true;
        this.railMove.launch();

        this.lazyLoad()
    }

    trigger(e) {
        if(e.obj.el != this.el || (window.isMobile && !this.isMobile)) {
            return;
        }

        if(e.way === 'enter') {
            this.active = true

            if(!this.launched) {
                this.launch()
            } else {
                this.railMove.pause()
                this.railMove.resume()
            }

        } else {
            this.active = false
            this.railMove.pause()
        }
    }

    lazyLoad() {
        this.lazy = this.$('lazy')

        for(let el of Array.from(this.lazy)) {
            let src = this.getData('lazy', el)
            if(src.length) {
                let img = new Image()

                let loadCallback = () => {
                    if(el.tagName == "IMG") {
                        el.src = src
                    } else {
                        el.style.backgroundImage = `url(${src})`
                    }

                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            let lazyParent = queryClosestParent(el, '.o-lazy_wrapper')
                            if(lazyParent) lazyParent.classList.add('is-loaded')
                            el.classList.add('is-loaded')
                        })
                    })
                }

                // Try to use `decode` if available (to prevent jank), otherwise use the standard `onload`
                if(img.decode) {
                    img.src = src
                    img.decode().then(loadCallback).catch(err => {
                        console.error(err)
                    })
                } else {
                    img.onload = loadCallback
                    img.src = src
                }

                this.setData('lazy', '', el)
            }
        }
    }

    onScroll(args) {
        this.railMove.updateScroll(args)
    }

    destroy() {
        super.destroy();
    }
}