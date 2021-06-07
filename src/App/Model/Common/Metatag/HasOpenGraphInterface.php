<?php

namespace App\Model\Common\Metatag;

/**
 * Defines descriptive metadata for the
 * {@link http://ogp.me OpenGraph} Protocol.
 *
 * Provided meta element properties:
 * - "og:card" — The `opengraph_type` property
 * - "og:image" — The `opengraph_image` or `meta_image` properties
 * - "og:title" — The `meta_title` property
 * - "og:description" — The `meta_description` property
 *
 * Recommended elements:
 * - "og:url" — The canonical URL of your object
 * - "og:site_name" — Name of website
 *
 * @see /metadata/app/model/common/metatag/has-open-graph-interface.json
 */
interface HasOpenGraphInterface extends HasMetadataInterface
{
    /**
     * The OpenGraph namespace prefix.
     *
     * @var string
     */
    const OPENGRAPH_NAMESPACE = 'og';

    /**
     * The default OpenGraph content type.
     *
     * @var string
     */
    const DEFAULT_OPENGRAPH_TYPE = 'website';

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
     * @return string|null
     */
    public function getOpengraphImage();
}
