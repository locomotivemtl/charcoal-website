<?php

declare(strict_types=1);

namespace App\Action;

use App\Action\AbstractAction;
use Pimple\Container;

/**
 * Action: Process Form Submissions
 */
abstract class AbstractFormAction extends AbstractAction
{
    /**
     * @var \Charcoal\Factory\FactoryInterface
     */
    protected $emailFactory;

    /**
     * @var bool
     */
    private $retry = true;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $errors = [];



    // Controller
    // =========================================================================

    /**
     * @return array
     */
    public function results()
    {
        return [
            'success' => $this->success(),
            'retry'   => $this->getRetry(),
            'message' => $this->getMessage(),
            'errors'  => $this->getErrors(),
        ];
    }

    /**
     * @param  bool $retry Whether the user can retry submission.
     * @return void
     */
    public function setRetry(bool $retry) : void
    {
        $this->retry = $retry;
    }

    /**
     * @return bool
     */
    public function getRetry() : bool
    {
        return $this->retry;
    }

    /**
     * @param  ?string $message The state description.
     * @return void
     */
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function hasMessage() : bool
    {
        return !!$this->message;
    }

    /**
     * @return string|null
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }

    /**
     * @param  array $errors Many errors.
     * @return void
     */
    public function setErrors(array $errors) : void
    {
        $this->errors = $errors;
    }

    /**
     * @param  string $ident   The result ident.
     * @param  string $message The validation message.
     * @return void
     */
    public function addError(string $ident, string $message) : void
    {
        if (!isset($this->errors[$ident])) {
            $this->errors[$ident] = [];
        }

        $this->errors[$ident][] = $message;
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function countErrors() : int
    {
        return count($this->errors);
    }



    // Dependencies
    // =========================================================================

    /**
     * @param  Container $container The service container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->emailFactory = $container['email/factory'];
    }
}
