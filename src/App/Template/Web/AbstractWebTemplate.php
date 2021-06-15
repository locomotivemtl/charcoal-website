<?php

namespace App\Template\Web;

use App\Template\AbstractTemplate;

// From 'pimple/pimple'
use Pimple\Container;

use App\Model\Page;

/**
 * Web Template Controller
 */
abstract class AbstractWebTemplate extends AbstractTemplate
{
    /**
     * @var bool
     */
    private $isCaptchaEnabled;

    /**
     * @var bool
     */
    private $isCaptchaInvisible;

    /**
     * @var array
     */
    private $featuresSummary;


    // Templating
    // =========================================================================

    /**
     * @var array
     */
    public function featuresSummary() {
        if ($this->featuresSummary === null) {
            $this->featuresSummary = array_map(function($value) {
                return ($this->featuresSummaryTransformer)($value);
            }, $this->featuresRepository->reset()->load());

        }
        return $this->featuresSummary;
    }

    /**
     * @param  Container $container
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->featuresRepository  = $container['app/collection-loaders']['feature'];
        $this->featuresSummaryTransformer = $container['app/transformers']['feature/summary'];
    }

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

    /**
     * Determines if a CAPTCHA test is available.
     *
     * @return bool
     */
    public function isCaptchaEnabled() : bool
    {
        if ($this->isCaptchaEnabled === null) {
            $recaptcha = $this->appConfig('apis.google.recaptcha');

            if (empty($recaptcha) || (isset($recaptcha['active']) && $recaptcha['active'] === false)) {
                return $this->isCaptchaEnabled = false;
            }

            $this->isCaptchaEnabled = (!empty($recaptcha['public_key']) && !empty($recaptcha['private_key']));
        }

        return $this->isCaptchaEnabled;
    }

    /**
     * Determines if the CAPTCHA test is enabled and invisible.
     *
     * @return bool
     */
    public function isCaptchaInvisible() : bool
    {
        if ($this->isCaptchaInvisible === null) {
            $this->isCaptchaInvisible = false;

            if ($this->isCaptchaEnabled()) {
                $recaptcha = $this->appConfig('apis.google.recaptcha');

                $hasInvisible = isset($recaptcha['invisible']);
                if ($hasInvisible && $recaptcha['invisible'] === true) {
                    return $this->isCaptchaInvisible = true;
                }

                $hasSize = isset($recaptcha['size']);
                if ($hasSize && $recaptcha['size'] === 'invisible') {
                    return $this->isCaptchaInvisible = true;
                }

                if (!$hasInvisible && !$hasSize) {
                    return $this->isCaptchaInvisible = true;
                }
            }
        }

        return $this->isCaptchaInvisible;
    }

    /**
     * Used for menu & footer routes
     *
     * @return array
     */
    public function routes() {
        if(!$this->routes) {
            $this->routes = [
                'home' => (string) $this->modelFactory()->create(Page::class)->loadFrom('template_ident', 'home')->url(),
                'technology' => (string) $this->modelFactory()->create(Page::class)->loadFrom('template_ident', 'technology')->url(),
                'contact' => (string) $this->modelFactory()->create(Page::class)->loadFrom('template_ident', 'contact')->url()
            ];
        }

        return $this->routes;
    }

    /**
     * @return Generator
     */
    public function langSwitcherItems()
    {
        $available = array_flip($this->availableLanguages());
        $available[$this->currentLanguage()] = [
            'isCurrent' => true,
        ];
        $alternate = iterator_to_array($this->alternateTranslations());
        $items = array_merge($available, $alternate);
        foreach ($items as $langCode => $transStruct) {
            yield $langCode => $transStruct;
        }
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

    /**
     * @return string
     */
    public function isFrench()
    {
        return $this->currentLanguage() === 'fr';
    }

}
