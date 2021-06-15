<?php

namespace App\Template;

use Charcoal\Cms\AbstractWebTemplate as CharcoalTemplate;
use Charcoal\Model\ModelInterface;

use App\Model\Common\Metatag;

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
    const ASSETS_VERSION = '202106081505';


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

    // Content Blocks
    // -------------------------------------------------------------------------

    /**
     * @param  array|null     $model
     * @param  ModelInterface $context
     * @return void
     */
    protected function prepareContentBlocks(&$model, ModelInterface $context)
    {
        if (!empty($model) && isset($model['blocks']) && $model['hasBlocks']) {
            /** @return \Generator */
            $loop = function () use ($model, $context) {
                foreach ($model['blocks'] as $block) {
                    $this->prepareContentBlocks($block, $context);
                    $view = ($block['__VIEW__'] ?? null);
                    if ($view) {
                        $context->setDynamicTemplate('block', $view);
                        yield $block;
                    }
                }
            };

            $model['blocks'] = $loop();
        }
    }

    // Metadata
    // -------------------------------------------------------------------------

    /**
     * Retrieve the title of the page (the context).
     *
     * @override AbstractWebTemplate::title()
     *
     * @return string|null
     */
    public function title()
    {
        $context = $this->contextObject();

        if ($context) {
            if (isset($context['title'])) {
                return $context['title'];
            }

            if (isset($context['name'])) {
                return $context['name'];
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @override AbstractWebTemplate::metaTitle()
     *     Replaces {@see \Charcoal\Cms\MetatagInterface} with {@see \App\Model\Common\Metatag}.
     *
     * @return string|null
     */
    public function metaTitle()
    {
        $context = $this->contextObject();

        if ($context instanceof Metatag\HasMetatagInterface) {
            $title = (string)$context['metaTitle'];
        } else {
            $title = null;
        }

        if (empty($title)) {
            $title = (string)$this->fallbackMetaTitle();
        }

        return $title;
    }

    /**
     * {@inheritdoc}
     *
     * @override AbstractWebTemplate::metaDescription()
     *     Replaces {@see \Charcoal\Cms\MetatagInterface} with {@see \App\Model\Common\Metatag}.
     *
     * @return string|null
     */
    public function metaDescription()
    {
        $context = $this->contextObject();

        if ($context instanceof Metatag\HasMetatagInterface) {
            $desc = (string)$context['metaDescription'];
        } else {
            $desc = null;
        }

        if (empty($desc)) {
            $desc = (string)$this->fallbackMetaDescription();
        }

        return $desc;
    }

    /**
     * {@inheritdoc}
     *
     * @override AbstractWebTemplate::metaImage()
     *     Replaces {@see \Charcoal\Cms\MetatagInterface} with {@see \App\Model\Common\Metatag}.
     *
     * @return string|null
     */
    public function metaImage()
    {
        $context = $this->contextObject();

        $img = null;
        if ($context instanceof Metatag\HasMetatagInterface) {
            $img = (string)$context['metaImage'];
        }

        if (empty($img)) {
            $img = (string)$this->fallbackMetaImage();
        }

        return $this->resolveMetaImage($img);
    }

    /**
     * {@inheritdoc}
     *
     * @override AbstractWebTemplate::opengraphType()
     *     Replaces {@see \Charcoal\Cms\MetatagInterface} with {@see \App\Model\Common\Metatag}.
     *
     * @return string|null
     */
    public function opengraphType()
    {
        $context = $this->contextObject();

        if ($context instanceof Metatag\HasOpenGraphInterface) {
            $type = $context['opengraphType'];
        } else {
            $type = null;
        }

        if (empty($type)) {
            $type = Metatag\HasOpenGraphInterface::DEFAULT_OPENGRAPH_TYPE;
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     *
     * @override AbstractWebTemplate::opengraphImage()
     *     Replaces {@see \Charcoal\Cms\MetatagInterface} with {@see \App\Model\Common\Metatag}.
     *
     * @return string|null
     */
    public function opengraphImage()
    {
        $context = $this->contextObject();

        if ($context instanceof Metatag\HasOpenGraphInterface) {
            $img = (string)$context['opengraphImage'];
        } else {
            $img = null;
        }

        if (empty($img)) {
            return $this->metaImage();
        }

        return $this->resolveMetaImage($img);
    }

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function twitterCardType()
    {
        $context = $this->contextObject();

        if ($context instanceof Metatag\HasTwitterCardInterface) {
            $type = $context['twitterCardType'];
        } else {
            $type = null;
        }

        if (empty($type)) {
            $type = Metatag\HasTwitterCardInterface::DEFAULT_TWITTER_CARD_TYPE;
        }

        return $type;
    }

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return string|null
     */
    public function twitterCardImage()
    {
        $context = $this->contextObject();

        if ($context instanceof Metatag\HasTwitterCardInterface) {
            $img = (string)$context['twitterCardImage'];
        } else {
            $img = null;
        }

        if (empty($img)) {
            return $this->metaImage();
        }

        return $this->resolveMetaImage($img);
    }
}
