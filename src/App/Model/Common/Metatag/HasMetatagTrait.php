<?php

namespace App\Model\Common\Metatag;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Basic implementation of {@see HasMetatagInterface}
 *
 * This trait is recommended for models.
 */
trait HasMetatagTrait
{
    /**
     * Name of the object.
     *
     * @var Translation|string|null
     */
    protected $metaTitle;

    /**
     * Description of the object.
     *
     * @var Translation|string|null
     */
    protected $metaDescription;

    /**
     * Thumbnail of the object.
     *
     * @var Translation|string|null
     */
    protected $metaImage;

    /**
     * Retrieve the object's name or title.
     *
     * In a basic metadata structure, this should appear
     * in the `<title>` element.
     *
     * With the OpenGraph Metadata—as it should appear
     * in the graph—for the "og:title" meta-property.
     *
     * @return Translation|string|null
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set the object's name or title.
     *
     * @param  string $name The name for the object.
     * @return self
     */
    public function setMetaTitle($name)
    {
        $this->metaTitle = $this->property('metaTitle')->parseVal($name);

        return $this;
    }

    /**
     * Retrieve the object's description.
     *
     * @return Translation|string|null
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set the object's description.
     *
     * @param  string $description A short description for the object.
     * @return self
     */
    public function setMetaDescription($description)
    {
        $this->metaDescription = $this->property('metaDescription')->parseVal($description);

        return $this;
    }

    /**
     * Retrieve the object's thumbnail or preview image.
     *
     * @return Translation|string|null
     */
    public function getMetaImage()
    {
        return $this->metaImage;
    }

    /**
     * Set the object's thumbnail or preview image.
     *
     * @param  string $path A path to an image.
     * @return self
     */
    public function setMetaImage($path)
    {
        $this->metaImage = $this->property('metaImage')->parseVal($path);

        return $this;
    }

    /**
     * Retrieve an instance of {@see PropertyInterface} for the given property.
     *
     * @see    \Charcoal\Property\DescribablePropertyInterface
     * @param  string $propertyIdent The property (ident) to get.
     * @return \Charcoal\Property\PropertyInterface
     */
    abstract public function property($propertyIdent);
}
