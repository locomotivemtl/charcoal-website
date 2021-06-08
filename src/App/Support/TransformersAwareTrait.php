<?php

namespace App\Support;

use RuntimeException;

// From 'pimple/pimple'
use Pimple\Container;

/**
 * Support: Transformers Aware Trait
 *
 * Provides awareness of the transformer store.
 */
trait TransformersAwareTrait
{
    /**
     * @var Container
     */
    private $transformers;

    /**
     * @param Container $transformers
     * @return void
     */
    protected function setTransformers(Container $transformers)
    {
        $this->transformers = $transformers;
    }

    /**
     * @param  string $key The transformer to retrieve.
     * @return AbstractTransformer
     */
    protected function getTransformer($key)
    {
        if (!isset($this->transformers)) {
            throw new RuntimeException(sprintf(
                'Transformers Store has not been set on "%s".',
                get_called_class()
            ));
        }
        return $this->transformers[$key];
    }
}
