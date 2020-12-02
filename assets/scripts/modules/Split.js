import { module } from 'modujs';

export default class extends module {
    constructor(m) {
        super(m);
    }

    init() {

        const param = this.getData('type') !== null ? this.getData('type') : 'lines';

        setTimeout(() => {
            let splitContent = new SplitText(this.el, {type: param});
            this.el.classList.add('is-split');

            if(param === 'lines') {
                const lines = this.el.querySelectorAll('div');

                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i];
                    const lineInner = line.innerHTML;

                    line.innerHTML = '<div></div>'

                    line.querySelector('div').innerHTML = lineInner;
                }
            }

        }, 600);

    }
}
