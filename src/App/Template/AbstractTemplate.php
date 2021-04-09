<?php

namespace App\Template;

use Charcoal\Cms\AbstractWebTemplate as CharcoalTemplate;

/**
 * Base Template Controller
 */
abstract class AbstractTemplate extends CharcoalTemplate
{
    /**
     * Static assets versionning.
     *
     * @var string
     */
    const ASSETS_VERSION = '1.0.0';


    // Templating
    // =========================================================================

    /**
     * Retrieve the assets version for cache busting.
     *
     * @return string
     */
    final public function assetsVersion() : string
    {
        return self::ASSETS_VERSION;
    }

    /**
     * @return string
     */
    public function copyright() : string
    {
        return sprintf('© %s %s', $this->copyrightName(), $this->copyrightYear());
    }

    /**
     * @return string
     */
    public function copyrightName() : string
    {
        return $this->appConfig('project_name');
    }

    /**
     * Retrieve current year (for copyright info).
     *
     * @return string
     */
    public function copyrightYear() : string
    {
        return date('Y');
    }

    /**
     * Loop X number of times.
     *
     * @return array
     */
    public function forLoop() : array
    {
        $i = 0;
        $max = 50;
        $out = [];
        for (; $i < $max; $i++) {
            $k = 1;
            $mini = [];
            for (; $k <= $i; $k++) {
                $mini[] = $k;
            }

            $out[(string)$i] = $mini;
        }

        return $out;
    }

    /**
     * Retrieve the site name.
     *
     * @return string
     */
    public function siteName() : string
    {
        return $this->appConfig('project_name');
    }
}
