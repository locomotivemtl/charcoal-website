<?php

namespace App\Model\Common;

use Charcoal\Object\RoutableInterface;
use Charcoal\Translator\Translation;

/**
 * Basic implementation of {@see HasToggleableLanguagesInterface}
 */
trait HasToggleableLanguagesTrait
{
    /**
     * Languages of the object.
     *
     * @var string|null
     */
    private $languages;

    /**
     * A flag determining if the object is available in English.
     *
     * @var boolean
     */
    private $isEn;

    /**
     * A flag determining if the object is available in French.
     *
     * @var boolean
     */
    private $isFr;

    /**
     * A flag determining if the object is available in all languages.
     *
     * @var boolean
     */
    private $isAll;

    /**
     * @param  array $languages
     * @return void
     */
    public function setLanguages($languages)
    {
        $this->languages = $this->property('languages')->parseVal($languages);
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param  boolean $flag
     * @return self
     */
    public function setIsEn($flag)
    {
        $this->isEn = $this->property('isEn')->parseVal($flag);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsEn()
    {
        return $this->isEn;
    }

    /**
     * @param  boolean $flag
     * @return self
     */
    public function setIsFr($flag)
    {
        $this->isFr = $this->property('isFr')->parseVal($flag);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsFr()
    {
        return $this->isFr;
    }

    /**
     * @param  boolean $flag
     * @return self
     */
    public function setIsAll($flag)
    {
        $this->isAll = $this->property('isAll')->parseVal($flag);
    }

    /**
     * @return boolean
     */
    public function getIsAll()
    {
        return $this->isAll;
    }

    /**
     * Quick way to determine if current object can be served in current language.
     *
     * @return boolean
     */
    public function isAvailableInCurrentLanguage()
    {
        return boolval($this['is_'.$this->translator()->getLocale()]);
    }

    /**
     * Resolve the slugs that needs to be generated. Set as empty any slugs that are not
     * supported by the selected languages.
     *
     * @return void
     */
    protected function saveSlug()
    {
        if ($this['isAll'] === false && $this instanceof RoutableInterface) {
            $slug      = $this['slug'];
            $slugs     = ($slug instanceof Translation) ? $slug->data() : $slug;
            $languages = $this['languages'];

            foreach ($slugs as $key => $value) {
                if (!in_array($key, $languages)) {
                    $slugs[$key] = '';
                }
            }

            $this['slug'] = $slugs;
        }
    }

    /**
     * Resolve the language object values.
     *
     * @return void
     */
    protected function saveLanguageProperties()
    {
        $languages  = $this['languages'];
        $isEn       = false;
        $isFr       = false;

        if (!$languages) {
            return false;
        }

        foreach ($languages as $key) {
            switch ($key) {
                case 'en':
                    $isEn = true;
                    break;
                case 'fr':
                    $isFr = true;
                    break;
            }
        }

        $this['isAll'] = ($isEn && $isFr);
        $this['isEn']  = $isEn;
        $this['isFr']  = $isFr;
    }

    /**
     * Resolve the available routes according to language. Delete the unnecessary
     * ObjectRoute models for languages not supported by the object.
     *
     * @return void
     */
    protected function validateRoutes()
    {
        if ($this['isAll'] === false && $this instanceof RoutableInterface) {
            $slug      = $this['slug'];
            $slugs     = ($slug instanceof Translation) ? $slug->data() : $slug;
            $languages = $this['languages'];

            foreach ($slugs as $key => $value) {
                if (!in_array($key, $languages)) {
                    // Recursive cleaning up.
                    while ($route = $this->getLatestObjectRoute($key)) {
                        if ($route->id() !== null) {
                            $route->delete();
                        } else {
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @return boolean
     */
    protected function validateLanguages()
    {
        if (empty($this['languages'])) {
            $this->validator()->error($this->translator()->translate(
                [
                    'en' => 'No language is selected.',
                    'fr' => 'Aucun language n\'est sélectionné.',
                ]
            ));
            return false;
        }
        return true;
    }

    /**
     * @return Translator
     */
    abstract protected function translator();

    /**
     * @return ValidatorInterface
     */
    abstract public function validator();
}
