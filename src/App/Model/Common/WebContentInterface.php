<?php

namespace App\Model\Common;

// From 'charcoal-object'
use Charcoal\Object\RoutableInterface;

// From App
use App\Model\Common\Metatag;

/**
 * Routable Model Interface
 *
 * @charcoal-metadata
 */
interface WebContentInterface extends
    Metatag\HasMetatagInterface,
    Metatag\HasOpenGraphInterface,
    Metatag\HasTwitterCardInterface,
    RoutableInterface
{
    /**
     * Retrieve the model's URI.
     *
     * @return string|null
     */
    public function url();

    /**
     * Retrieve the model's URI endpoint.
     *
     * @return string|null
     */
    public function endpointSlug();

    /**
     * Create the model's URI slug.
     *
     * @return string|null
     */
    public function createSlug();
}
