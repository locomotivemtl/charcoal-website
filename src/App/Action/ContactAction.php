<?php

declare(strict_types=1);

namespace App\Action;

use App\Model\Contact\Entry;
use App\Model\Contact\Notification;
use App\Exception\Http\ClientException;
use App\Exception\Http\ServerException;
use App\Presenter\Mail\ContactPresenter;
use Charcoal\Support\Model\Repository\ModelCollectionLoader;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Action: Process Contact Form Submission
 */
class ContactAction extends AbstractFormAction
{
    /**
     * @var ModelCollectionLoader
     */
    private $notificationRepository;



    // Controller
    // =========================================================================

    /**
     * @param  RequestInterface  $request  The HTTP request.
     * @param  ResponseInterface $response The HTTP response.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        $this->setSuccess(false);
        $this->setRetry(true);

        try {
            $entry = $this->saveFormEntry($request);
        } catch (ClientException $e) {
            $this->logger->warning(sprintf(
                '[App] Contact: Failed to process form entry (#%s): %s',
                ($entry['id'] ?? 0),
                $e->getMessage()
            ));

            return $response->withStatus(400);
        } catch (Throwable $t) {
            $this->logger->error(sprintf(
                '[App] Contact: Failed to save form entry (#%s): %s',
                ($entry['id'] ?? 0),
                $t->getMessage()
            ));

            return $response->withStatus(500);
        }

        $this->sendEmailNotifications($entry);

        $this->setSuccess(true);
        $this->setRetry(false);

        return $response->withStatus(201);
    }

    /**
     * Creates the form entry model from the HTTP request.
     *
     * @param  RequestInterface $request The HTTP request.
     * @throws InvalidFormException If the form data is invalid.
     * @return Entry
     */
    protected function saveFormEntry(RequestInterface $request) : Entry
    {
        $entry = $this->modelFactory()->create(Entry::class);
        $props = $entry->getSubmittablePropertyNames();

        $data = $request->getParams($props);
        $data = $entry->sanitizeFormData($data);
        $errs = $entry->validateFormData($data);

        if (count($errs) > 0) {
            $this->setErrors($errs);

            throw new ClientException(sprintf(
                'Invalid form fields: %s',
                implode(', ', array_keys($errs))
            ));
        }

        $entry->setData($data);
        $entry['lang']   = $this->translator()->getLocale();
        $entry['origin'] = getenv('HTTP_REFERER') ?: null;

        if (!$entry->save()) {
            throw new ServerException(sprintf(
                'Entry was not saved'
            ));
        }

        return $entry;
    }

    /**
     * Sends any email notifications.
     *
     * @param  Entry $entry The form submission.
     * @return void
     */
    protected function sendEmailNotifications(Entry $entry) : void
    {
        $notifications = $this->getContactCategoryRepository()->cursorAll();
        foreach ($notifications as $notification) {
            try {
                $presenter = new ContactPresenter(
                    $notification,
                    $entry,
                    $this->baseUrl(),
                    $this->translator()
                );

                $data = $presenter->getEmailData();
                $data['campaign'] = 'contact:'.$notification['id'];

                $email = $this->emailFactory->create('email');
                $email->setData($data);

                if (!$email->send()) {
                    throw new ServerException(sprintf(
                        'Email was not sent'
                    ));
                }
            } catch (Throwable $t) {
                $this->logger->error(sprintf(
                    '[App] Contact: Failed to send notification (#%s): %s',
                    ($notification['id'] ?? 0),
                    $t->getMessage()
                ));
            }
        }
    }



    // Dependencies
    // =========================================================================

    /**
     * @return ModelCollectionLoader
     */
    protected function getContactCategoryRepository() : ModelCollectionLoader
    {
        return $this->notificationRepository->reset(true);
    }

    /**
     * @param  Container $container The service container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->notificationRepository = $container['app/collection-loaders']['contact/notification'];
    }
}
