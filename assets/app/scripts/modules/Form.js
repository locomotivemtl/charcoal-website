
import { module } from 'modujs';
import { isNil } from '../utils/is';
import { isDebug } from '../utils/environment';

const CLASS = {
    IS_LOADING: 'is-loading',
    IS_SUCCESS: 'is-success',
    IS_WARNING: 'is-warning',
    IS_ERRORED: 'is-errored',
};

/**
 * Returns the given value, optionally passed through the given callback.
 *
 * @param  {*}        value
 * @param  {function} callback
 * @return {*}
 */
const attempt = (value, callback) => {
    if (typeof callback === 'function') {
        if (!isNil(value)) {
            return callback(value);
        }
    }

    return value;
};

export default class extends module {
    constructor(m) {
        super(m);

        this.events = {
            input:  'onInput',
            submit: 'onSubmit',
        };
    }

    init() {
        isDebug && console.log('[App.Form] Initializing…');

        this.successMessage  = this.getData('success-message');
        this.warningMessage  = this.getData('warning-message');
        this.errorMessage    = this.getData('error-message');
        this.criticalMessage = this.getData('critical-message');

        this.$form     = this.el;
        this.$feedback = this.$('feedback')[0];
    }

    /**
     * Fires when a form control has been changed.
     *
     * This method resets the error state of the form control.
     *
     * @param {Event} event - The input event.
     */
    onInput(event) {
        if (this.isFormInvalid() && event.target.validationMessage) {
            event.target.setCustomValidity('');
            event.target.checkValidity();
        }
    }

    /**
     * Fires when a form is submitted.
     *
     * This method defers the submission of the form.
     *
     * @param {Event} event - The submit event.
     */
    onSubmit(event) {
        isDebug && console.log('[App.Form.onSubmit] Handling "submit" event:', event);
        event.preventDefault();

        if (this.isFormBusy()) {
            console.warn('Form is busy');
            return false;
        }

        if (this.isFormCompleted()) {
            console.info('Form is submitted');
            return false;
        }

        event.target.checkValidity();

        try {
            this.submitForm();
        } catch (error) {
            console.error('[App.Form.onSubmit]', error);
            this.setErroredState(this.errorMessage);
        }
    }

    /**
     * Submits form.
     */
    submitForm() {
        this.setLoadingState();

        /*
        this.$form.querySelectorAll('.c-form_control').forEach(input => {
            input.removeAttribute('aria-invalid');

            attempt(
                document.getElementById(`${input.id}-field`),
                (elem) => elem.classList.remove(CLASS.IS_INVALID)
            );

            attempt(
                document.getElementById(`${input.id}-error`),
                (elem) => {
                    elem.setAttribute('aria-hidden', true);
                    elem.innerHTML = '';
                }
            );
        });
        */

        let isRedirected  = false;
        let isSuccessful  = false;
        let isClientError = false;
        let isServerError = false;

        const formUrl    = this.$form.action;
        const formMethod = this.$form.method;
        const formData   = new FormData(this.$form);
        const controller = new AbortController();

        fetch(formUrl, {
            method: formMethod,
            body:   formData,
            signal: controller.signal,
        }).then(response => {
            if (response.url && response.url != formUrl) {
                isRedirected = true;
                isSuccessful = true;

                try {
                    isDebug && console.log('[App.Form.submitForm] Soft redirecting page…');
                    this.call('goTo', { url: response.url }, 'Load');
                } catch (e) {
                    isDebug && console.log('[App.Form.submitForm] Hard redirecting page…', e);
                    window.location.href = response.url;
                }

                controller.abort();

                /**
                 * In case window.location.href and AbortController fail,
                 * provide a dummy response object for the following thenable.
                 */
                return {
                    success: true,
                    retry: false,
                };
            } else if (response.status >= 200 && response.status <= 299) {
                isSuccessful = true;
            } else if (response.status >= 400 && response.status <= 499) {
                isClientError = true;
            } else if (response.status >= 500 && response.status <= 599) {
                isServerError = true;
            }

            return response.json();
        }).then(response => {
            isDebug && console.log('[App.Form.submitForm~then]', { isRedirected, isSuccessful, isClientError, isServerError, response });

            // Check for false-positives
            if (isSuccessful && response.success === false) {
                isSuccessful = false;

                if (response.retry) {
                    isClientError = true;
                } else {
                    isServerError = false;
                }
            }

            if (isServerError) {
                this.setErroredState(response.message || this.errorMessage);
            } else if (isClientError) {
                this.setWarningState(response.message || this.warningMessage);

                let invalidInputs = []

                /** Delaying validity to allow the scrollTo of {@see this.setFormState()} to complete. */
                window.setTimeout(() => {
                    for (let [ inputName, errors ] of Object.entries(response.errors)) {
                        attempt(
                            errors.length && inputName && document.getElementById(inputName),
                            (input) => {
                                for (let error of errors) {
                                    input.setCustomValidity(error);
                                    input.reportValidity();
                                    /** Reporting only the first error for simplicity */
                                    break
                                }
                            }
                        );
                    }
                }, 900);
            } else {
                this.setSuccessState(response.message || this.successMessage);

                if (!isRedirected) {
                    this.$form.reset();
                }
            }
        }).catch(error => {
            isDebug && console.log('[App.Form.submitForm~catch]', { error });

            if (isRedirected) {
                this.setSuccessState(this.successMessage);
            } else {
                console.error('[App.Form.submitForm]', error);
                this.setErroredState(this.criticalMessage);
            }
        })
    }

    /**
     * @return {boolean}
     */
    isFormBusy() {
        this.$form.classList.contains(CLASS.IS_LOADING);
    }

    /**
     * @return {boolean}
     */
    isFormCompleted() {
        this.$form.classList.contains(CLASS.IS_SUCCESS);
    }

    /**
     * @return {boolean}
     */
    isFormInvalid() {
        this.$form.classList.contains(CLASS.IS_INVALID);
    }

    /**
     * @return {boolean}
     */
    isFormErrored() {
        this.$form.classList.contains(CLASS.IS_ERRORED);
    }

    /**
     * Applies a loading state on the form.
     *
     * @param  {string} [message] - The message to show.
     * @return {void}
     */
    setLoadingState(message) {
        this.setFormState(CLASS.IS_LOADING, message);
    }

    /**
     * Applies a success state on the form.
     *
     * @param  {string} [message] - The message to show.
     * @return {void}
     */
    setSuccessState(message) {
        this.setFormState(CLASS.IS_SUCCESS, message);
    }

    /**
     * Applies an error state on the form.
     *
     * @param  {string} [message] - The message to show.
     * @return {void}
     */
    setWarningState(message) {
        this.setFormState(CLASS.IS_WARNING, message);
    }

    /**
     * Applies an error state on the form.
     *
     * @param  {string} [message] - The message to show.
     * @return {void}
     */
    setErroredState(message) {
        this.setFormState(CLASS.IS_ERRORED, message);
    }

    /**
     * Applies a state on the form.
     *
     * @param  {string} className - The CSS class to add to the form.
     * @param  {string} [message] - The message to show.
     * @return {void}
     */
    setFormState(className, message) {
        this.clearFormState();

        this.$form.classList.add(className);

        if (typeof message === 'string') {
            this.$feedback.innerHTML = message;
        }

        // this.updateScroll();
    }

    /**
     * Clears any state on the form.
     *
     * @return {void}
     */
    clearFormState() {
        this.$form.classList.remove(CLASS.IS_SUCCESS);
        this.$form.classList.remove(CLASS.IS_LOADING);
        this.$form.classList.remove(CLASS.IS_WARNING);
        this.$form.classList.remove(CLASS.IS_ERRORED);

        this.$feedback.innerHTML = '';

        // this.updateScroll('clear');
    }

    /**
     * If locomotive-scroll is used, refreshes the page height.
     *
     * @param {string} [state] - The scroll update context.
     * @param {void}
     */
    updateScroll(status) {
        requestAnimationFrame(() => {
            if (status !== 'clear') {
                this.call('scrollTo', this.$form, 'Scroll');
            }

            this.call('update', null, 'Scroll');
        });
    }
}
