
import { module } from 'modujs';

const CLASS = {
    IS_SUCCESS: 'is-success',
    IS_LOADING: 'is-loading',
    IS_ERROR: 'is-error'
}

export default class extends module {
    constructor(m) {
        super(m);

        this.events = {
            submit: 'submit'
        }
    }

    init() {
    
        this.confirmMessage = this.getData('confirm-message');
        this.errorMessage = this.getData('error-message');

        this.$feedback = this.$('feedback')[0];
    
    }

    /**
     * Set loading state (show loader, fade form, and other UI)
     *
     * @param {none}
     */
    setLoadingState() {
        this.el.classList.add(CLASS.IS_LOADING)
    }

    /**
     * Set confirmation state;
     * Display confirmation message
     *
     * @param {string} | confirmation message
     */
    setConfirmationState(message) {
        this.clearState()

        this.el.classList.add(CLASS.IS_SUCCESS)
        this.$feedback.innerHTML = message

        // this.updateScroll()
    }

    /**
     * Set error state
     *
     * @param {message} | form error message
     */
    setErrorState(message) {
        this.clearState()

        this.el.classList.add(CLASS.IS_ERROR)
        this.$feedback.innerHTML = message

        // this.updateScroll()
    }

    /**
     * Clear all state
     *
     * @param
     */
    clearState() {
        this.el.classList.remove(CLASS.IS_SUCCESS)
        this.el.classList.remove(CLASS.IS_LOADING)
        this.el.classList.remove(CLASS.IS_ERROR)

        this.$feedback.innerHTML = ""

        // this.updateScroll('clear')
    }

    /**
     * In case of use of locomotive-scroll, we need to update page height
     *
     * @param {none}
     */
    updateScroll(status) {
        requestAnimationFrame(() => { 
            if(status !== 'clear') {
                this.call('scrollTo', (this.el), 'Scroll') ;
            }

            this.call('update', null, 'Scroll') ;
        })
    }

    /**
     * Custom submit
     *
     * @param {e} | event
     */
    submit(e) {
        e.preventDefault();

        this.clearState()
        this.setLoadingState()

        let success = Math.floor(Math.random() * Math.floor(2));

        //fake submit behavior
        setTimeout(() => {
            if(success) {
                console.log('Submit : succes')
                this.setConfirmationState(this.confirmMessage)
            } else {
                console.log('Submit : Error')
                this.setErrorState(this.errorMessage)
            }
        }, 1500);
    }
}