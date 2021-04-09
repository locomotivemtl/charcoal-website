<?php

namespace App\Model\Contact;

use App\Model\Common\ModelInterface;
use Charcoal\Object\Content;
use Charcoal\Translator\Translation;

/**
 * Model: Contact Notification
 *
 * Represents an email notification after form submission from the {@see \App\Template\Web\ContactTemplate Contact page}.
 *
 * @charcoal-metadata
 */
class Notification extends Content implements ModelInterface
{
    /**
     * @var ?string The name to identify the notification.
     */
    public $name;

    /**
     * @var ?string The 'From' email address.
     */
    public $from;

    /**
     * @var ?string The 'Reply-To' email address.
     */
    public $replyTo;

    /**
     * @var string[] One or many 'To' email addresses.
     */
    private $to = [];

    /**
     * @var string[] One or many 'CC' email addresses.
     */
    private $cc = [];

    /**
     * @var string[] One or many 'BCC' email addresses.
     */
    private $bcc = [];

    /**
     * @var ?Translation The subject.
     */
    private $subject;

    /**
     * @var ?Translation Rich-text message
     */
    private $msgHtml;

    /**
     * @var ?Translation Plan-text message.
     */
    private $msgTxt;

    /**
     * @param  mixed $emails One or many email addresses.
     * @return void
     */
    public function setTo($emails) : void
    {
        $this->to = (array)$this->property('to')->parseVal($emails);
    }

    /**
     * @return string[]
     */
    public function getTo() : array
    {
        return $this->to;
    }

    /**
     * @param  mixed $emails One or many email addresses.
     * @return void
     */
    public function setCc($emails) : void
    {
        $this->cc = (array)$this->property('cc')->parseVal($emails);
    }

    /**
     * @return string[]
     */
    public function getCc() : array
    {
        return $this->cc;
    }

    /**
     * @param  mixed $emails One or many email addresses.
     * @return void
     */
    public function setBcc($emails) : void
    {
        $this->bcc = (array)$this->property('bcc')->parseVal($emails);
    }

    /**
     * @return string[]
     */
    public function getBcc() : array
    {
        return $this->bcc;
    }

    /**
     * @param  mixed $subject The subject.
     * @return void
     */
    public function setSubject($subject) : void
    {
        $this->subject = $this->property('subject')->parseVal($subject);
    }

    /**
     * @return ?Translation
     */
    public function getSubject() : ?Translation
    {
        return $this->subject;
    }

    /**
     * @param  mixed $text The rich-text message.
     * @return void
     */
    public function setMsgHtml($text) : void
    {
        $this->msgHtml = $this->property('msgHtml')->parseVal($text);
    }

    /**
     * @return ?Translation
     */
    public function getMsgHtml() : ?Translation
    {
        return $this->msgHtml;
    }

    /**
     * @param  mixed $text The rich-text message.
     * @return void
     */
    public function setMsgTxt($text) : void
    {
        $this->msgTxt = $this->property('msgTxt')->parseVal($text);
    }

    /**
     * @return ?Translation
     */
    public function getMsgTxt() : ?Translation
    {
        return $this->msgTxt;
    }

    /**
     * Alias of {@see self::getActive()}.
     *
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->getActive();
    }

    /**
     * Retrieves a map of available Contact Entry properties.
     *
     * @return array<string, string>
     */
    public function getContactEntryTemplateTags() : array
    {
        return $this->modelFactory()->get(Entry::class)->getTemplatableTags();
    }
}
