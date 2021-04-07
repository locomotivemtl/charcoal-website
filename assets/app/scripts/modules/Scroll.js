import { module } from 'modujs';
import LocomotiveScroll from 'locomotive-scroll';
import { html } from '../utils/environment';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {

        setTimeout(() => {

            html.classList.add('has-scroll-module-init');

            this.scroll = new LocomotiveScroll({
                el: this.el,
                smooth: true,
                getDirection: true,
                offset: ['15%']
            });

            this.scroll.on('call', (func,way,obj,id) => {
                // Using modularJS
                this.call(func[0],{way,obj},func[1],func[2]);
            });

            this.scroll.on('scroll', (args) => {
                // console.log(args.scroll);

                if(args.scroll.y > 50) {
                    html.classList.add('has-scrolled');
                } else {
                    html.classList.remove('has-scrolled');
                }

                if(args.direction == 'down') {
                    html.classList.add('is-scrolling-down')
                } else {
                    html.classList.remove('is-scrolling-down')
                }

                // this.call('checkScroll','Object3D');

                if(typeof args.currentElements['hero'] === 'object') {
                    let progress = args.currentElements['hero'];
                    this.call('onScroll', [progress, args], 'Hero');
                }
            });
        }, 200);
    }

    toggleLazy(args) {
        let src = this.getData('lazy', args.obj.el)
        if(src.length) {
            if(args.obj.el.tagName == "IMG") {
                args.obj.el.src = src
            } else {
                args.obj.el.style.backgroundImage = `url(${src})`
            }
            this.setData('lazy', '', args.obj.el)
        }
    }

    destroy() {
        this.scroll.destroy();
        html.classList.remove('has-scroll-module-init');
        html.classList.remove('is-scrolling-down')
        html.classList.remove('has-scrolled');

    }
}
