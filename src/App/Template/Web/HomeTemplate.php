<?php

namespace App\Template\Web;

// From 'pimple/pimple'
use Pimple\Container;

/**
 * Template Controller: Front Page
 */
class HomeTemplate extends AbstractWebTemplate
{

    /**
     * @var array
     */
    private $caseStudies;

    /**
     * @param  Container $container
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->caseStudiesRepository  = $container['app/collection-loaders']['case-study'];
        $this->caseStudiesTransformer = $container['app/transformers']['case-study'];
    }

    /**
     * @var array
     */
    public function caseStudies() {
        if ($this->caseStudies === null) {
            $this->caseStudies = array_map(function($caseStudy) {
                return ($this->caseStudiesTransformer)($caseStudy);
            }, $this->caseStudiesRepository->reset()->load());

        }
        return $this->caseStudies;
    }
}
