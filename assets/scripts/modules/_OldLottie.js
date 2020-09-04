import { APP_NAME } from '../utils/environment';
import { module } from 'modujs';

const MODULE_NAME = 'LottieHeader';
const EVENT_NAMESPACE = `${APP_NAME}.${MODULE_NAME}`;

const EVENT = {
    INVIEW: `inview.${EVENT_NAMESPACE}`
};

const PATH_TO_MOTION_FILES = `/assets/lottie-theme-${window.colorsetIndex}/`

const OPTIONS_DEFAULT = {
    loop: true,
    autoplay: true,
    animType: 'svg'
}

export default class extends module {
    constructor(m) {
        super(m);

        this.$el = $(this.el)
        this.$container = this.$el
        this.container = this.$container[0]
        this.fileName = this.getData('file');
    }

    init() {
        // fix mask white?
        if(window.isSafari) lottie.setLocationHref(document.location.href);

        this.lottiePromise = this.loadAnimationByName(this.fileName).then(data => {
            if(this.toDestroy) return

            let lottieAnim = lottie.loadAnimation({
                container: this.container,
                animType: OPTIONS_DEFAULT.animType,
                loop: OPTIONS_DEFAULT.loop,
                autoplay: OPTIONS_DEFAULT.autoplay,
                animationData: data,
                rendererSettings: {
                    preserveAspectRatio: 'xMidYMid slice'
                }
            })

            this.animation = {
                lottieAnim,
                data
            }

            let lottiePromise = new Promise((resolve, reject) => {
                lottieAnim.addEventListener('DOMLoaded', () => {
                    resolve()

                    // this.$el.on(EVENT.INVIEW, (e) => {
                    //     if(e.way == "enter") {
                    //         this.animation.lottieAnim.play()
                    //     } else {
                    //         this.animation.lottieAnim.pause()
                    //     }
                    // })

                    window.lottiePromises.splice(window.lottiePromises.indexOf(this.lottiePromise), 1)
                })
            })

            return lottiePromise
        }).catch((err) => {
            console.error(err)
        });

        // requieres window.lottiePromises = [] in globals.js
        window.lottiePromises.push(this.lottiePromise)
    }

    /**
     * This function fetches (in a promise) the data of the given animation (found by it's name)
     * By doing so, it also re-maps the assets URLs to match the good folder
     *
     * @param {string} name - The filename of the animation to load (with extension)
     * @returns {Promise}
     */
    loadAnimationByName(name) {
        return new Promise((resolve, reject) => {
            fetch(PATH_TO_MOTION_FILES+name+'.json') // get the json
             .then((resp) => resp.json()) // parse it
             .then(data => {
                // re-map all assets URLs to match the good folder
                data.assets.map(item => {
                    if(item.u && item.u.length) {
                        item.u = PATH_TO_MOTION_FILES+item.u;
                    }
                    return item;
                })

                // return the data
                resolve(data);
            })
            .catch((err) => {
                reject(err);
            })
        })
    }

    toggle(e) {
        if(this.animation && this.animation.lottieAnim && this.animation.lottieAnim.play && this.animation.lottieAnim.pause) {
            if(e.way === "enter") {
                this.animation.lottieAnim.play()
            } else {
                this.animation.lottieAnim.pause()
            }
        }
    }

    destroy() {
        super.destroy();
        this.$el.off(`.${EVENT_NAMESPACE}`);

        this.toDestroy = true

        let actualDestroy = () => {
            if(this.animation && this.animation.lottieAnim && this.animation.lottieAnim.destroy)
                this.animation.lottieAnim.destroy()
            this.animation.lottieAnim = null
        }
    }
}
