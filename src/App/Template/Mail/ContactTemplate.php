<?php

declare(strict_types=1);

namespace App\Template\Mail;

use App\Model\Contact\Entry;
use App\Model\Contact\Notification;
use App\Presenter\Mail\ContactPresenter;
use ArrayObject;
use DateTimeImmutable;
use ValueError;

/**
 * Template Controller: Contact Notification
 */
class ContactTemplate extends AbstractMailTemplate
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var ArrayObject
     */
    public $fields;

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

    // Dependencies
    // =========================================================================

    /**
     * {@inheritdoc}
     *
     * @param  array $data Key-value array of data to append.
     * @return self
     */
    public function setData(array $data)
    {
        parent::setData($data);

        if (isset($data['notification_id'])) {
            $presenter = new ContactPresenter(
                $this->getNotification(),
                $this->getEntry(),
                $this->baseUrl(),
                $this->translator()
            );

            $data = $presenter->getEmailTemplateData();

            parent::setData($data);
        }

        return $this;
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
