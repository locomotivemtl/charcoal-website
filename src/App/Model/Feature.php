<?php

namespace App\Model;

// From 'pimple/pimple'
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoaderAwareTrait;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;
use Charcoal\Cms\TemplateableTrait;

// From 'charcoal-attachment'
use Charcoal\Attachment\Interfaces\AttachmentAwareInterface;
use Charcoal\Attachment\Traits\AttachmentAwareTrait;

// From App
use App\Model\Common\AbstractWebContent;

use function App\Support\fill_missing_translations;

/**
 * Model: Feature
 */
class Feature extends AbstractWebContent implements
    AttachmentAwareInterface,
    TemplateableInterface
{
    use AttachmentAwareTrait,
        CollectionLoaderAwareTrait,
        TemplateableTrait;

    const REPOSITORY_CACHE_KEY_PREFIX = 'object/app.model.feature.id.';
    const ROUTE_TEMPLATE = 'feature';

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var string
     */
    private $thumbnailVideo;

    /**
     * @var string The {@see \App\Model\Common\Metatag\HasTwitterCardTrait::$twitterCardType Twitter card type}.
     */
    protected $twitterCardType = 'summary';

    /**
     * @var string The {@see \App\Model\Common\Metatag\HasOpenGraphTrait::$opengraphType OpenGraph type}.
     */
    protected $opengraphType = 'website';

    /**
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }



    // Properties
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

    /**
     * @param  mixed $title
     * @return void
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $this->property('subtitle')->parseVal($subtitle);
    }

    /**
     * @return string|null
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param  string $path
     * @return void
     */
    public function setThumbnail($path)
    {
        $this->thumbnail = $this->property('thumbnail')->parseVal($path);
    }

    /**
     * @return string|null
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param  string $path
     * @return void
     */
    public function setThumbnailVideo($path)
    {
        $this->thumbnailVideo = $this->property('thumbnailVideo')->parseVal($path);
    }

    /**
     * @return string|null
     */
    public function getThumbnailVideo()
    {
        return $this->thumbnailVideo;
    }

    // Aliases/Implementations
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function choiceLabel()
    {
        return $this->parseChoiceLabel('name');
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

        $locale = $this->translator()->getLocale();
        $slug = [
            $this->translator()->translate('route.features.endpointSlug'),
        ];
        array_unshift($slug, $locale);
        return implode('/', $slug);
    }

    /**
     * Create the model's URI slug.
     *
     * @return string|null
     */
    public function createSlug()
    {
        // Clone it, manipulate it, don't reference it.
        $title = clone $this['title'];

        return $this->slugify((string)$title);
    }

    // Validation
    // -------------------------------------------------------------------------

    /**
     * @return array
     */
    protected function validatableProperties()
    {
        return [
            'title' => 'title'
        ];
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
        $this->saveTemplateOptions();

        $result = parent::preSave();

        return $result;
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

        $result = parent::preUpdate($properties);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function postDelete()
    {
        $this->deleteAttachments();

        return parent::postDelete();
    }



    // Dependencies
    // -------------------------------------------------------------------------

    /**
     * @param Container $container
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setCollectionLoader($container['model/collection/loader']);
    }
}
