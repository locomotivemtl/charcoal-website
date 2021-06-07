<?php

namespace App\Model\Common\Metatag;

/**
 * Defines an entity that accepts an ob
 * Defines a content model as having descriptive metadata.
 *
 * For promotion on the Internet (e.g., linked data, syndication, searchability).
 */
interface MetadataAwareInterface
{
    /**
     * Retrieve the canonical URI of the object.
     *
     * @return string|null
     */
    public function getCanonicalUrl();

    /**
     * Retrieve the current URI of the object.
     *
     * @return string|null
     */
    public function getCurrentUrl();

    /**
     * Retrieve the name or title of the object.
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Retrieve the description of the object.
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Retrieve the URL to the image representing the object.
     *
     * @return string|null
     */
    public function getMetaImage();

    /**
     * Retrieve the object's {@link https://developers.facebook.com/docs/reference/opengraph/ OpenGraph type},
     * for the "og:type" meta-property.
     *
     * @return string|null
     */
    public function getOpengraphType();

    /**
     * Retrieve the URL to the object's social image for the "og:image" meta-property.
     *
     * This method can fallback onto {@see MetadataInterface::defaultMetaImage()}
     * for a common image between web annotation schemas.
     *
     * @return string|null
     */
    public function getOpengraphImage();

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function getTwitterCardType();

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return string|null
     */
    public function getTwitterCardImage();
}
