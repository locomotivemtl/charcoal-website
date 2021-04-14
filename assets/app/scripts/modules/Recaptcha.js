import { module } from 'modujs';
import { isDebug } from '../utils/environment';

const RECAPTCHA_JS_ID = 'airinuit-recaptcha-js'

/**
 * Google reCAPTCHA Controller
 */
export default class extends module {
    /**
     * Ensure the JS API is available.
     */
    init() {
        isDebug && console.log('[App.Recaptcha] Initializing…')

        this.active = false;

        if (!document.getElementById(RECAPTCHA_JS_ID) && (!window.grecaptcha || !window.grecaptcha.render)) {
            this.loadJsApi()
        }

        this.attempt = 0;
        this.poller  = window.setInterval(() => {
            this.attempt++;

            if (this.attempt === 50) {
                isDebug && console.warn('[App.Recaptcha] JS API is unavailable')
                clearInterval(this.poller)
                this.active = false
                return
            }

            if (!window.grecaptcha || !window.grecaptcha.render) {
                return
            }

            this.active = true;
            clearInterval(this.poller)
            this.render()
        }, 100)
    }

    /**
     * Fetch the JS API.
     */
    loadJsApi() {
        isDebug && console.log('[App.Recaptcha] Loading JS API…')

        const js = document.getElementsByTagName('script')[0]
        const re = document.createElement('script')

        re.id    = RECAPTCHA_JS_ID
        re.async = true
        re.src   = 'https://www.google.com/recaptcha/api.js?hl=' + document.documentElement.lang + '&render=explicit'

        js.parentNode.insertBefore(re, js)
    }

    /**
     * Retrieves the reCAPTCHA element.
     *
     * @return {?Element}
     */
    getElement() {
        const recaptcha = this.el.querySelector('.c-form_recaptcha')
        if (recaptcha) {
            return recaptcha
        }

        return null
    }

    /**
     * Prepare the DOM element(s).
     */
    render() {
        isDebug && console.log('[App.Recaptcha] Rendering…')

        const elem = this.getElement()
        if (!elem) {
            return
        }

        const params = {
            'sitekey':  elem.getAttribute('data-sitekey'),
            'theme':    elem.getAttribute('data-theme'),
            'tabindex': elem.getAttribute('data-tabindex'),
        }

        if (elem.hasChildNodes()) {
            return
        }

        let callback = false
        if (elem.getAttribute('data-size') === 'invisible') {
            callback = (token) => {
                isDebug && console.log('[App.Recaptcha] Dispatching event "recaptcha-executed"')
                const event = new CustomEvent('recaptcha-executed', {
                    bubbles: true,
                    detail:  token,
                })

                this.el.dispatchEvent(event)
            }
        }

        if (callback) {
            params.callback = callback
        }

        elem.setAttribute('data-widget-id', grecaptcha.render(elem, params))

        if (params.tabindex) {
            elem.querySelector('iframe').setAttribute('tabindex', params.tabindex)
        }
    }

    /**
     * Reset the reCAPTCHA element.
     */
    reset() {
        isDebug && console.log('[App.Recaptcha] Resetting…')

        const recaptcha = this.getElement()
        if (recaptcha && recaptcha.getAttribute('data-widget-id')) {
            grecaptcha.reset(recaptcha.getAttribute('data-widget-id'))
        }
    }

    destroy() {
        clearInterval(this.poller)
    }
}
