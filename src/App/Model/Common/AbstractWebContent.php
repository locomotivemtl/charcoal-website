<?php

namespace App\Model\Common;

use App\Model\Common\AbstractContent;
use App\Model\Common\Metatag;
use App\Model\Common\WebContentInterface;
use Charcoal\Object\RoutableTrait;
use Pimple\Container;
use Psr\Http\Message\UriInterface;

/**
 * Model: Base Routable Content
 */
abstract class AbstractWebContent extends AbstractContent implements
    WebContentInterface
{
    use Metatag\HasMetatagTrait,
        Metatag\HasOpenGraphTrait,
        Metatag\HasTwitterCardTrait;
    use RoutableTrait {
        RoutableTrait::slugify as slugifyFromTrait;
    }



    // Routing
    // -------------------------------------------------------------------------

    /**
     * @return boolean
     */
    protected function isRoutable()
    {
        return true;
    }

    /**
     * Retrieve the model's URI endpoint.
     *
     * @return string|null
     */
    public function endpointSlug()
    {
        return null;
    }

    /**
     * Convert a string into a slug.
     *
     * This method strips punctuation to replicate slugs from Charcoal Legacy.
     *
     * @param  string $str The string to slugify.
     * @return string The slugified string.
     */
    public function slugify($str)
    {
        // Remove punctuation
        $punctuation = '/[&%\?\!\(\)\[\]\{\}\\\"\':#\.,;]/';
        $str = preg_replace($punctuation, '', $str);

        return $this->slugifyFromTrait($str);
    }



    // Hooks
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function preSave()
    {
        $this->setSlug($this->generateSlug());

        if (empty($this['templateIdent']) && defined('static::ROUTE_TEMPLATE')) {
            $this->setTemplateIdent(static::ROUTE_TEMPLATE);
        }

        return parent::preSave();
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function postSave()
    {
        if (defined('static::ROUTE_TEMPLATE')) {
            $data = [
                'route_template' => static::ROUTE_TEMPLATE,
            ];
        } else {
            $data = [];
        }

        $metadata = $this->metadata();
        if ($this->isRoutable()) {
            $this->generateObjectRoute($this->getSlug(), $data);
        }

        return parent::postSave();
    }

    /**
     * {@inheritdoc}
     *
     * @param  string[]|null $properties One or many properties to be updated.
     * @return boolean
     */
    protected function preUpdate(array $properties = null)
    {
        $this->setSlug($this->generateSlug());

        return parent::preUpdate($properties);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string[]|null $properties One or many properties that were updated.
     * @return boolean
     */
    protected function postUpdate(array $properties = null)
    {
        if (defined('static::ROUTE_TEMPLATE')) {
            $data = [
                'route_template' => static::ROUTE_TEMPLATE,
            ];
        } else {
            $data = [];
        }

        $metadata = $this->metadata();
        if ($this->isRoutable()) {
            $this->generateObjectRoute($this->getSlug(), $data);
        }

        return parent::postUpdate($properties);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function postDelete()
    {
        $this->deleteObjectRoutes();

        return parent::postDelete();
    }
}
