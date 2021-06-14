<?php

namespace App\Model;

// From 'pimple/pimple'
use Pimple\Container;

use Charcoal\Cms\TemplateableInterface;
use Charcoal\Cms\TemplateableTrait;

// From App
use App\Support\AdminAwareTrait;
use App\Model\Common\AbstractWebContent;

/**
 * Class Page
 */
class Page extends AbstractWebContent implements
    TemplateableInterface
{
    use AdminAwareTrait,
        TemplateableTrait;

    /**
     * @var string
     */
    private $title;

    // Hooks
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function preSave()
    {
        $this->saveTemplateOptions();

        return parent::preSave();
    }

    /**
     * {@inheritdoc}
     *
     * This method skips slug regeneration in {@see AbstractWebContent::preUpdate()}.
     *
     * @param  string[]|null $properties One or many properties to be updated.
     * @return boolean
     */
    protected function preUpdate(array $properties = null)
    {
        $props = [ 'template_ident', 'template_options', 'templateIdent', 'templateOptions' ];
        if (empty($properties) || array_intersect($properties, $props)) {
            $this->saveTemplateOptions();
        }

        return parent::preUpdate($properties);
    }

    // Routing
    // -------------------------------------------------------------------------

    /**
     * Retrieve the model's URI endpoint.
     *
     * @return string|null
     */
    public function endpointSlug()
    {
        return $this->translator()->getLocale();
    }

    /**
     * Create the model's URI slug.
     *
     * @return string|null
     */
    public function createSlug()
    {
        // Clone it, manipulate it, don't reference it.
        $title = (string) $this['title'];

        return $this->slugify((string)$title);
    }

    // GET / SET
    // -------------------------------------------------------------------------

    /**
     * @param  mixed $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $this->property('title')->parseVal($title);
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }
}
