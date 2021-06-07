<?php

namespace App\Model\Common\Metatag;

/**
 * Defines a model using HTML meta elements.
 *
 * @see /metadata/app/model/common/metatag/has-metatag-interface.json
 */
interface HasMetatagInterface extends HasMetadataInterface
{
    /**
     * Retrieve the object's name or title.
     *
     * With the Basic Metadata structure, this should appear
     * in the `<title>` element.
     *
     * With the OpenGraph Metadata—as it should appear
     * in the graph—for the "og:title" meta-property.
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Retrieve the object's description.
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Retrieve the object's thumbnail or preview image.
     *
     * @return string|null
     */
    public function getMetaImage();
}
