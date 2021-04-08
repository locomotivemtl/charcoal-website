<?php

namespace App\Template\Web;

// From App
use App\Template\Web\AbstractTemplate;

/**
 * Template Controller: Front Page
 */
class HomeTemplate extends AbstractTemplate
{
    /**
     * @return string
     */
    public function test()
    {
        return 'TEST '.rand(0, 100);
    }
}
