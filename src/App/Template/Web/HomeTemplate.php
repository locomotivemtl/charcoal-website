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
     * @var string
     */
    private $introExcerpt;

    /**
     * @var string
     */
    private $introText1;

    /**
     * @var string
     */
    private $introText2;

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

    /**
     * @param string $value
     * @return void
     */
    public function setIntroExcerpt($value)
    {
        $this->introExcerpt = $this->translator()->translation($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getIntroExcerpt()
    {
        return $this->introExcerpt;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setIntroText1($value)
    {
        $this->introText1 = $this->translator()->translation($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getIntroText1()
    {
        return $this->introText1;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setIntroText2($value)
    {
        $this->introText2 = $this->translator()->translation($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getIntroText2()
    {
        return $this->introText2;
    }
}
