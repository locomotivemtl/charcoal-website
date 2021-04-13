<?php

namespace App\Template\Mail;

use App\Template\AbstractTemplate;

/**
 * Mail Template Controller
 */
abstract class AbstractMailTemplate extends AbstractTemplate
{
    /**
     * @var ?string The email subject line.
     */
    public $subject;

    /**
     * @var ?string The email body.
     */
    public $message;

    /**
     * @param  mixed $subject The email subject.
     * @return void
     */
    public function setSubject($subject) : void
    {
        $this->subject = $this->translator()->translate($subject);
    }

    /**
     * @return bool
     */
    public function hasSubject() : bool
    {
        return (is_string($this->subject) && $this->subject);
    }

    /**
     * @param  mixed $message The email message.
     * @return void
     */
    public function setMessage($message) : void
    {
        $this->message = $this->translator()->translate($message);
    }

    /**
     * @return bool
     */
    public function hasMessage() : bool
    {
        return (is_string($this->message) && $this->message);
    }
}
