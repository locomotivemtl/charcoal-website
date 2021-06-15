<?php

namespace App\Template\Web;

/**
 * Template Controller: Technology
 */
class TechnologyTemplate extends AbstractWebTemplate
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
