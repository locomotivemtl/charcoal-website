<?php

declare(strict_types=1);

namespace App\Template\Web;

/**
 * Template Controller: Contact
 */
class ContactTemplate extends AbstractWebTemplate
{
    // Templating
    // =========================================================================

    /**
     * Generate a string containing HTML attributes for the <form> element.
     *
     * @return string
     */
    public function formAttrs() : string
    {
        return html_build_attributes($this->getFormAttributes());
    }

    // =========================================================================

    /**
     * Retrieve the <form> element attribute structure.
     *
     * @return array<string, mixed>
     */
    protected function getFormAttributes() : array
    {
        $translator = $this->translator();

        $attrs = [
            'id'      => 'c-contact-form',
            'class'   => [
                'c-header-contact_form',
                'u-anim -delay-5',
            ],
            'action'  => '/api/v1/contact',
            'method'  => 'POST',

            'data-module-form'           => true,
            'data-form-success-message'  => $translator->trans('form.success.message'),
            'data-form-warning-message'  => $translator->trans('form.warning.message'),
            'data-form-error-message'    => $translator->trans('errorTryAgain'),
            'data-form-critical-message' => $translator->trans('errorTryLater'),
        ];

        return $attrs;
    }
}
