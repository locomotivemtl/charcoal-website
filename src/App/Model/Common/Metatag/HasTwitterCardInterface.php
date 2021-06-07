<?php

namespace App\Model\Common\Metatag;

/**
 * Defines descriptive metadata for
 * {@link https://dev.twitter.com/cards/overview Twitter Cards}.
 *
 * Provided meta element properties:
 * - "twitter:card" — The `twitter_type` property
 * - "twitter:image" — The `twitter_image` or `meta_image` properties
 * - "twitter:title" — The `meta_title` property
 * - "twitter:description" — The `meta_description` property
 *
 * Recommended elements:
 * - "twitter:url" — The canonical URL of your object
 * - "twitter:site" — @username of website
 * - "twitter:creator" — The object's author (i.e., the `created_by` property)
 *
 * @see /metadata/app/model/common/metatag/has-twitter-card-interface.json
 */
interface HasTwitterCardInterface extends HasMetadataInterface
{
    /**
     * The Twitter Card namespace prefix.
     *
     * @var string
     */
    const TWITTER_CARD_NAMESPACE = 'twitter';

    /**
     * The default Twitter Card type.
     *
     * @var string
     */
    const DEFAULT_TWITTER_CARD_TYPE = 'summary';

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
