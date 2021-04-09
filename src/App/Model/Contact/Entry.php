<?php

namespace App\Model\Contact;

use App\Model\Common\ModelInterface;
use Charcoal\Object\UserData;

/**
 * Model: Contact Entry
 *
 * Represents a form submission from the {@see \App\Template\Web\ContactTemplate Contact page}.
 *
 * @charcoal-metadata
 */
class Entry extends UserData implements ModelInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $message;

    /**
     * Retrieves a map of properties that can be used in templates.
     *
     * @return array<string, string>
     */
    public function getTemplatableTags() : array
    {
        $tags = [];

        foreach ($this->properties() as $prop) {
            $tags[] = [
                'tag'  => '{{ ' . $prop['ident'] . ' }}',
                'desc' => (string)$prop['description'],
            ];
        }

        return $tags;
    }
}
