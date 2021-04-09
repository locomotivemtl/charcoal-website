<?php

namespace App\Template\Web;

use App\Template\AbstractTemplate;

/**
 * Web Template Controller
 */
abstract class AbstractWebTemplate extends AbstractTemplate
{
    // Templating
    // =========================================================================

    /**
     * @return array
     */
    public function api() : array
    {
        return $this->appConfig('apis');
    }

    /**
     * Retrieve the critical stylesheet contents.
     *
     * @return ?string
     */
    public function criticalCss() : ?string
    {
        $filePath = $this->appConfig()->publicPath().'assets/styles/critical.css';

        if (file_exists($filePath)) {
            ob_start();
            echo file_get_contents($filePath);
            return ob_get_clean();
        }

        return null;
    }

    /**
     * Generate a string containing HTML attributes for the <html> element.
     *
     * @return string
     */
    public function htmlAttr() : string
    {
        return html_build_attributes($this->getHtmlAttributes());
    }

    // =========================================================================

    /**
     * Retrieve the <html> element attribute structure.
     *
     * @return array<string, mixed>
     */
    protected function getHtmlAttributes() : array
    {
        $attrs = [
            'class'         => [ 'has-no-js' ],
            'lang'          => $this->currentLanguage(),
            'data-template' => $this->templateName(),
            'data-debug'    => $this->debug(),
        ];

        return $attrs;
    }
}
