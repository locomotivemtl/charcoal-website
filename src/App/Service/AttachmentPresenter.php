<?php

namespace App\Service;

use RuntimeException;

// From 'pimple/pimple'
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-presenter'
use Charcoal\Presenter\Presenter;

/**
 * The Locomotive Attachment Presenter Service.
 *
 * Manages the storing of attachment presenter instances and formatting of attachments.
 */
class AttachmentPresenter
{
    /**
     * The Pimple DI Container.
     *
     * @var Container $container
     */
    private $container;

    /**
     * @var array
     */
    private $transformers = [];

    /**
     * @var array
     */
    private $presenters = [];

    /**
     * AttachmentPresenter constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->container = $data['container'];

        foreach ($data['transformers'] as $key => $value) {
            $this->addTransformer($key, $value);
        }
    }

    /**
     * Add an attachment transformer to transformer store.
     *
     * @param string $objType
     * @param array  $transformer
     * @throws RuntimeException If a transformer has already been set for an objType.
     * @return self
     */
    protected function addTransformer($objType, $transformer)
    {
        if (isset($this->transformers[$objType])) {
            throw new RuntimeException(sprintf(
                'A Transformer already exists in %s for objType "%s".',
                get_class(),
                $objType
            ));
        }

        $this->transformers[$objType] = $transformer;

        return $this;
    }

    /**
     * Create and retrieve an attachment presenter instance.
     *
     * @param string $objType
     * @throws RuntimeException If an objType is not set in the transformer store.
     * @return Presenter
     */
    public function getPresenter($objType)
    {
        if (!isset($this->transformers[$objType])) {
            throw new RuntimeException(sprintf(
                'Presenter Identifier "%s" is not defined in %s.',
                $objType,
                get_class()
            ));
        }

        if (!isset($this->presenters[$objType])) {
            $this->presenters[$objType] = new Presenter($this->container['app/transformers/attachments'][$this->transformers[$objType]]);
        }

        return $this->presenters[$objType];
    }

    /**
     * Transform an attachment model or an array of attachment models.
     *
     * @param array|ModelInterface $models
     * @return array
     */
    public function transform($models)
    {
        if ($models instanceof ModelInterface) {
            $models = [ $models ];
        }

        $attachments = [];
        foreach ($models as $model) {
            $presenter = $this->getPresenter($model::objType());
            $attachments[] = $presenter->transform($model);
        }

        return $attachments;
    }
}
