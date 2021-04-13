<?php

declare(strict_types=1);

namespace App\Template\Mail;

use App\Model\Contact\Entry;
use App\Model\Contact\Notification;
use ArrayObject;
use DateTimeImmutable;
use ValueError;

/**
 * Template Controller: Contact Notification
 */
class ContactTemplate extends AbstractMailTemplate
{
    /**
     * @var array
     */
    public $fields;

    /**
     * @var array
     */
    public $admin;

    /**
     * @var Notification
     */
    private $notification;

    /**
     * @var Entry
     */
    private $entry;


    // Templating
    // =========================================================================

    /**
     * Returns a formatted email subject line using any Entry values.
     *
     * @return string
     */
    public function getSubject() : string
    {
        if (is_null($this->subject)) {
            $notification = $this->getNotification();

            $this->subject = (string)$notification['subject'];
            if ($this->subject) {
                $fields = $this->getFields()->getArrayCopy();
                $fields = array_column($fields, 'value', 'key');

                $this->subject = $notification->view()->renderTemplate(
                    $this->subject,
                    $fields
                );
            }
        }

        return $this->subject;
    }

    /**
     * Returns a formatted email message using any Entry fields and values.
     *
     * @return string
     */
    public function getMessage() : string
    {
        if (is_null($this->message)) {
            $notification = $this->getNotification();

            $this->message = (string)$this->getNotificationMessage();
            if ($this->message) {
                $fields = $this->getFields()->getArrayCopy();
                $fields['admin'] = $this->getAdmin();

                $this->message = $notification->view()->renderTemplate(
                    $this->message,
                    $fields
                );
            }
        }

        return $this->message;
    }

    /**
     * Returns the formatted fields for the Entry.
     *
     * @return ArrayObject
     */
    public function getFields() : ArrayObject
    {
        if (is_null($this->fields)) {
            $this->fields = new ArrayObject($this->getEntryFields());
        }

        return $this->fields;
    }

    /**
     * Returns the URL and HTML link to the Admin for the Entry.
     *
     * @return array
     */
    public function getAdmin() : array
    {
        if (is_null($this->admin)) {
            $entry = $this->getEntry();

            $this->admin = [
                'url'  => null,
                'link' => null,
            ];

            $this->admin['url'] = (string)$this->baseUrl()
                ->withPath('admin/object/edit')
                ->withQuery(http_build_query([
                    'obj_type' => $entry::objType(),
                    'obj_id'   => $entry['id'],
                ]));

            $this->admin['link'] = sprintf(
                '<a href="%s">%s</a>',
                htmlspecialchars($this->admin['url']),
                $this->translator()->trans('admin.app.contact.view-item')
            );
        }

        return $this->admin;
    }



    // Dependencies
    // =========================================================================

    /**
     * Returns the form submission fields.
     *
     * @return ?array
     */
    protected function getEntryFields() : ?array
    {
        $entry = $this->getEntry();

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
            $property = $entry->property($prop);

            $ident = $property['ident'];
            $value = $entry[$ident];
            $value = $entry->property($ident)->displayVal($value);

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

    /**
     * Returns the email notification message.
     *
     * @return ?string
     */
    protected function getNotificationMessage() : ?string
    {
        $notification = $this->getNotification();

        $message = trim((string)$notification['msgHtml']);
        if ($message) {
            return $message;
        }

        $message = trim((string)$notification['msgTxt']);
        if ($message) {
            return nl2br($message);
        }

        return null;
    }

    /**
     * @return Notification
     */
    protected function getNotification() : Notification
    {
        return $this->notification;
    }

    /**
     * @param  Notification $id The Notification object ID.
     * @return void
     * @throws ValueError If the Notification does not have an ID or is disabled.
     */
    protected function setNotification(Notification $model) : void
    {
        if (!$model['id'] || !$model['active']) {
            throw new ValueError('Notification does not exist');
        }

        $this->notification = $model;
    }

    /**
     * @param  string $id The Notification object ID.
     * @return void
     */
    protected function setNotificationId(string $id) : void
    {
        $model = $this->modelFactory()->create(Notification::class)->load($id);
        $this->setNotification($model);
    }

    /**
     * @return Entry
     */
    protected function getEntry() : Entry
    {
        if (is_null($this->entry)) {
            $this->entry = $this->createFakeEntry();
        }

        return $this->entry;
    }

    /**
     * @return Entry
     */
    protected function createFakeEntry() : Entry
    {
        $model = $this->modelFactory()->create(Entry::class);
        $model->setData([
            'id'      => 0,
            'name'    => 'John Doe',
            'email'   => 'john.doe@example.com',
            'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'lang'    => $this->currentLanguage(),
            'ts'      => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'origin'  => (string) $this->baseUrl()->withPath('/contact'),
            'ip'      => '127.0.0.1',
        ]);

        return $model;
    }

    /**
     * @param  Entry $id The Entry object ID.
     * @return void
     */
    protected function setEntry(Entry $model) : void
    {
        $this->entry = $model;
    }

    /**
     * @param  string $id The Entry object ID.
     * @return void
     */
    protected function setEntryId(string $id) : void
    {
        $model = $this->modelFactory()->create(Entry::class)->load($id);
        $this->setEntry($model);
    }
}
