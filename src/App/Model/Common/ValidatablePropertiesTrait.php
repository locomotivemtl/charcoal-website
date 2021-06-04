<?php

namespace App\Model\Common;

use App\Model\Common\HasToggleableLanguagesInterface;
use Charcoal\Translator\Translation;

/**
 * Better validation baby.
 */
trait ValidatablePropertiesTrait
{
    /**
     * @return boolean
     */
    public function validateProperties()
    {
        $translator = $this->translator();
        $locales    = $translator->locales();

        if ($this instanceof HasToggleableLanguagesInterface) {
            if ($this->validateLanguages() !== true) {
                return false;
            }
        }

        $result = true;
        $errors = [];
        foreach ($this->validatableProperties() as $propertyIdent => $propertyName) {
            $value = $this[$propertyIdent];
            if (empty($value)) {
                $this->validator()->error("The <strong>$propertyName</strong> field is required.");
                $result = false;
            }

            if ($value instanceof Translation) {
                if ($this instanceof HasToggleableLanguagesInterface) {
                    $languages   = $this['languages'];
                    $translator  = $this->translator();
                    $locales     = $translator->locales();
                    foreach ($value->data() as $lang => $value) {
                        // Use language property values in validation, since boolean states will not be set yet.
                        if (in_array($lang, $languages) && empty($value)) {
                            $errors[] = sprintf(
                                '- the <strong>%s</strong> field in %s',
                                $propertyName,
                                $translator->translation($locales[$lang]['name'])
                            );
                        }
                    }
                } else {
                    $values  = $value->data();
                    $filled  = array_keys(array_filter($values, 'strlen'));
                    $missing = array_values(array_diff(array_keys($values), $filled));
                    if (!empty($missing)) {
                        $missing = array_map(
                            function ($locale) use ($translator, $locales) {
                                return $translator->translate($locales[$locale]['name']);
                            },
                            $missing
                        );
                        $this->validator()->error(sprintf(
                            "The <strong>%s</strong> field is empty in the following languages: %s.",
                            $propertyName,
                            implode(', ', $missing)
                        ));
                        $result = false;
                    }
                }
            }
        }

        if (count($errors) > 0) {
            $this->validator()->error(sprintf(
                'This type of content requires:<br> %s',
                implode(', <br>', $errors)
            ));

            $result = false;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function validatableProperties()
    {
        return [];
    }

    /**
     * @return Translator
     */
    abstract protected function translator();
}
