<?php

namespace App\Model\Common;

/**
 * Defines language properties to determine the availability of each active language for an object.
 *
 * Implementation, as trait, provided by {@see HasToggleableLanguagesTrait}.
 */
interface HasToggleableLanguagesInterface
{
    /**
     * Retrieve the active language identifiers of the object.
     *
     * @return string
     */
    public function getLanguages();

    /**
     * Determines the availability of the object in English.
     *
     * @return boolean
     */
    public function getIsEn();

    /**
     * Determines the availability of the object in French.
     *
     * @return boolean
     */
    public function getIsFr();

    /**
     * Determines the availability of the object in all languages.
     *
     * @return boolean
     */
    public function getIsAll();

    /**
     * Determines if current object can be served in current language.
     *
     * @return boolean
     */
    public function isAvailableInCurrentLanguage();
}
