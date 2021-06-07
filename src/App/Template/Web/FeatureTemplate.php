<?php

namespace App\Template\Web;

// From 'pimple/pimple'
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

/**
 * Template Controller: Feature
 */
class FeatureTemplate extends AbstractWebTemplate
{

    /**
     * @param  Container $container
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $this->featureTransformer = $container['app/transformers']['feature'];
    }

    /**
     * @param  ModelInterface $context
     * @return self
     */
    public function setContextObject(ModelInterface $context)
    {
        $this->contextObject = $context;

        $feature = ($this->featureTransformer)($context);
        // $feature['contentBlocks'] = $this->parseContentBlocks($feature['contentBlocks']);
        $this['feature'] = $feature;
        return $this;
    }
}
