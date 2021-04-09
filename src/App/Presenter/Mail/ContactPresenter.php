<?php

declare(strict_types=1);

namespace App\Presenter\Mail;

use App\Model\Contact\Entry;
use App\Model\Contact\Notification;
use ArrayObject;
use Charcoal\Translator\Translator;
use Psr\Http\Message\UriInterface;

/**
 * Presenter: Contact Email Notification
 */
class ContactPresenter
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * @var Entry
     */
    private $entry;

    /**
     * @var UriInterface
     */
    private $baseUrl;

    /**
     * @param Notification $notification The email notification.
     * @param Entry        $entry        The form submission.
     * @param UriInterface $baseUrl      The application URI.
     * @param Translator   $translator   The translator service.
     */
    public function __construct(
        Notification $notification,
        Entry $entry,
        UriInterface $baseUrl,
        Translator $translator
    ) {
        $this->notification = $notification;
        $this->entry        = $entry;
        $this->baseUrl      = $baseUrl;
        $this->translator   = $translator;
    }

    /**
     * Returns the formatted email data.
     *
     * @return array
     */
    public function getEmailData() : array
    {
        $view = $this->notification->view();

        $fields = $this->getEntryFields();
        $values = array_column($fields, 'value', 'key');

        $renderValues = function ($templateString) use ($view, $values) : ?string {
            if ($templateString) {
                return $view->renderTemplate($templateString, $values);
            }

            return null;
        };

        $email = [];

        $addresses = $this->notification['to'];
        if (count($addresses) > 0) {
            $email['to'] = array_map($renderValues, $addresses);
        }

        $addresses = $this->notification['cc'];
        if (count($addresses) > 0) {
            $email['cc'] = array_map($renderValues, $addresses);
        }

        $addresses = $this->notification['bcc'];
        if (count($addresses) > 0) {
            $email['bcc'] = array_map($renderValues, $addresses);
        }

        $address = $this->notification['from'];
        if ($address) {
            $email['from'] = $view->renderTemplate($address, $values);
        }

        $address = $this->notification['replyTo'];
        if ($address) {
            $email['replyTo'] = $view->renderTemplate($address, $values);
        }

        $subject = (string)$this->notification['subject'];
        if ($subject) {
            $email['subject'] = $view->renderTemplate($subject, $values);
        }

        $email['template_ident'] = $this->notification['templateIdent'];
        $email['template_data']  = $this->getEmailTemplateData();

        $context = [
            'admin' => $this->getAdmin(),
        ] + $fields;

        $message = (string)$this->notification['msgTxt'];
        if ($message) {
            $email['msgTxt'] = nl2br($view->renderTemplate($message, $context));
        }

        return $email;
    }

    /**
     * Returns the formatted email template data.
     *
     * @return array
     */
    public function getEmailTemplateData() : array
    {
        $view = $this->notification->view();

        $fields = $this->getEntryFields();
        $values = array_column($fields, 'value', 'key');

        $templateData = [
            'fields' => new ArrayObject($fields),
        ];

        $subject = (string)$this->notification['subject'];
        if ($subject) {
            $templateData['subject'] = $view->renderTemplate($subject, $values);
        }

        $context = [
            'admin' => $this->getAdmin(),
        ] + $fields;

        $message = (string)$this->notification['msgTxt'];
        if ($message) {
            $templateData['message'] = nl2br($view->renderTemplate($message, $context));
        }

        $message = (string)$this->notification['msgHtml'];
        if ($message) {
            $templateData['message'] = $view->renderTemplate($message, $context);
        }

        return $templateData;
    }

    /**
     * Returns the URL and HTML link to the Admin for the Entry.
     *
     * @return array
     */
    protected function getAdmin() : array
    {
        $admin = [
            'url'  => null,
            'link' => null,
        ];

        $admin['url'] = (string)$this->baseUrl
            ->withPath('admin/object/edit')
            ->withQuery(http_build_query([
                'obj_type' => $this->entry::objType(),
                'obj_id'   => $this->entry['id'],
            ]));

        $admin['link'] = sprintf(
            '<a href="%s">%s</a>',
            htmlspecialchars($admin['url']),
            $this->translator->trans('admin.app.contact.view-item')
        );

        return $admin;
    }

    /**
     * Returns the form submission fields.
     *
     * @return array
     */
    protected function getEntryFields() : array
    {
        $props = [
            'id',
            'ts',
            'name',
            'email',
            'message',
            'lang',
            'origin',
        ];

        $fields = [];
        foreach ($props as $prop) {
            $property = $this->entry->property($prop);

            $ident = $property['ident'];
            $value = $this->entry[$ident];
            $value = $this->entry->property($ident)->displayVal($value);

            if (empty($value) && !is_numeric($value)) {
                continue;
            }

            $label = (string)$property['label'];
            $field = [
                'key'   => $ident,
                'label' => $label,
                'value' => $value,
            ];

            $fields[$ident] = $field;
        }

        if (isset($fields['message']['value'])) {
            $fields['message']['value'] = nl2br($fields['message']['value']);
        }

        return $fields;
    }
}
