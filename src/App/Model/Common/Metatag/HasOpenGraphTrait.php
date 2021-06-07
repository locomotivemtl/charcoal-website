<?php

namespace App\Model\Common\Metatag;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Basic implementation of {@see HasOpenGraphInterface}
 *
 * This trait is recommended for models.
 */
trait HasOpenGraphTrait
{
    /**
     * The object's OpenGraph {@link http://ogp.me/#types content type}.
     *
     * Depending on the type you specify, other properties may also be required.
     *
     * @var string|null
     */
    protected $opengraphType;

    /**
     * The path to object's thumbnail.
     *
     * @var Translation|string|null
     */
    protected $opengraphImage;

    /**
     * Retrieve the object's {@link https://developers.facebook.com/docs/reference/opengraph/ OpenGraph type},
     * for the "og:type" meta-property.
     *
     * @return string
     */
    public function getOpengraphType()
    {
        return $this->opengraphType;
    }

    /**
     * Set object's OpenGraph content type.
     *
     * @see    HasOpenGraphInterface::DEFAULT_OPENGRAPH_TYPE The default content type.
     * @param  string $type The OpenGraph content type.
     * @return self
     */
    public function setOpengraphType($type)
    {
        $this->opengraphType = $type;

        return $this;
    }

    /**
     * Retrieve the URL to the object's social image for the "og:image" meta-property.
     *
     * @return Translation|string|null
     */
    public function getOpengraphImage()
    {
        return $this->opengraphImage;
    }

    /**
     * Set the object's OpenGraph thumbnail image path.
     *
     * @param  string $path A path to an image.
     * @return self
     */
    public function setOpengraphImage($path)
    {
        $this->opengraphImage = $this->property('opengraphImage')->parseVal($path);

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
