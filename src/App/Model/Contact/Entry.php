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

    /**
     * Retrieves a map of properties that are user-fillable.
     *
     * @return string[]
     */
    public function getSubmittablePropertyNames() : array
    {
        return [
            'name',
            'email',
            'message',
        ];
    }

    /**
     * @param  array<string, mixed> $data The model data to check.
     * @return array<string, mixed>
     */
    public function sanitizeFormData(array $data) : array
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'email':
                    $data[$key] = trim(filter_var($value, FILTER_SANITIZE_EMAIL));
                    break;

                case 'name':
                case 'message':
                    $data[$key] = trim(filter_var($value));
                    break;

                default:
                    unset($data[$key]);
                    break;
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed> $data The model data to check.
     * @return array<string, string>
     */
    public function validateFormData(array $data) : array
    {
        $errors = [];

        foreach ($data as $key => $value) {
            if (strlen($value) === 0) {
                $errors[$key] = $this->translator()->trans('form.field.required');
                continue;
            }

            if ($key === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$key] = $this->translator()->trans('form.field.mismatch.email');
            }
        }

        return $errors;
    }
}
