<?php

namespace App\Model\Common\Metatag;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Basic implementation of {@see HasTwitterCardInterface}
 *
 * This trait is recommended for models.
 */
trait HasTwitterCardTrait
{
    /**
     * The object's Twitter card type.
     *
     * Depending on the type you specify, other properties may also be required.
     *
     * @var string|null
     */
    protected $twitterCardType;

    /**
     * The path to object's thumbnail.
     *
     * @var Translation|string|null
     */
    protected $twitterCardImage;

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function getTwitterCardType()
    {
        return $this->twitterCardType;
    }

    /**
     * Set object's Twitter card type or a fallback.
     *
     * @see    HasTwitterCardInterface::DEFAULT_TWITTER_CARD_TYPE The default card type.
     * @param  string $type The Twitter card type.
     * @return self
     */
    public function setTwitterCardType($type)
    {
        $this->twitterCardType = $type;

        return $this;
    }

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return Translation|string|null
     */
    public function getTwitterCardImage()
    {
        return $this->twitterCardImage;
    }

    /**
     * Set the object's Twitter Card thumbnail image path.
     *
     * @param  string $path A path to an image.
     * @return self
     */
    public function setTwitterCardImage($path)
    {
        $this->twitterCardImage = $this->property('twitterCardImage')->parseVal($path);

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
