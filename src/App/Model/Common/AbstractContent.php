<?php

namespace App\Model\Common;

use Charcoal\Cache\CachePoolAwareTrait;
use Charcoal\Object\Content;
use Charcoal\Validator\ValidatorInterface;
use Pimple\Container;
use App\Model\Common\ValidatablePropertiesTrait;

use function App\Support\fill_missing_translations;

/**
 * Model: Base Content
 */
abstract class AbstractContent extends Content implements
    ModelInterface
{
    use CachePoolAwareTrait;
    use ValidatablePropertiesTrait;

    const MIDDLEWARE_CACHE_KEY_PREFIX = 'request/get/';
    const TEMPLATING_CACHE_KEY_PREFIX = 'app/web/';

    /**
     * The base URI.
     *
     * @var UriInterface
     */
    private $baseUrl;

    /**
     * The default locale.
     *
     * @var string
     */
    protected $defaultLocale;



    // Aliases
    // -------------------------------------------------------------------------

    /**
     * Alias of {@see Content::getActive()}.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getActive();
    }

    /**
     * Determine if the object can be viewed (on the front-end).
     *
     * @return boolean
     */
    public function isViewable()
    {
        return $this->id() && $this->isActive();
    }

    /**
     * Get the object's name for collections of selectable options.
     *
     * This method presumes your model has a "name" property.
     *
     * @return string|null
     */
    public function choiceLabel()
    {
        return $this->parseChoiceLabel('name');
    }

    /**
     * Format the object's label for collections of selectable options.
     *
     * @param  string $key The model key to labelize.
     * @return string|null
     */
    protected function parseChoiceLabel($key)
    {
        $label = $this[$key];

        $metadata = $this->metadata();
        if (empty($metadata['properties'][$key]['l10n']['fallback'])) {
            return $label;
        }

        if ($label instanceof Translation) {
            $label = clone $label;
            fill_missing_translations($label, $metadata['properties'][$key]['l10n']['fallback']);
        }

        return $label;
    }



    // Validation
    // -------------------------------------------------------------------------

    /**
     * @param ValidatorInterface $v
     * @return boolean
     */
    public function validate(ValidatorInterface &$v = null)
    {
        if (parent::validate($v)) {
            return $this->validateProperties();
        } else {
            return false;
        }
    }



    // Hooks
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function postSave()
    {
        $result = parent::postSave();

        if ($result) {
            $this->deleteCacheItems();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string[]|null $properties One or many properties that were updated.
     * @return boolean
     */
    protected function postUpdate(array $properties = null)
    {
        $result = parent::postUpdate($properties);

        if ($result) {
            $this->deleteCacheItems();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function postDelete()
    {
        $result = parent::postDelete();

        if ($result) {
            $this->deleteCacheItems();
        }

        return $result;
    }



    // Cache
    // -------------------------------------------------------------------------

    /**
     * Retrieve multiple item keys for the cache pool.
     *
     * @return string[]
     */
    protected function getCacheItemKeys()
    {
        $cacheKeys = [];

        if (defined('static::REPOSITORY_CACHE_KEY_PREFIX')) {
            $cacheKeys[] = static::REPOSITORY_CACHE_KEY_PREFIX.$this->id();
        }

        if (defined('static::MIDDLEWARE_CACHE_KEY_PREFIX') && isset($this['url'])) {
            $uri = $this['url'];

            if ($uri instanceof Translation) {
                $paths = $uri->data();
                foreach ($paths as $path) {
                    $cacheKeys[] = static::MIDDLEWARE_CACHE_KEY_PREFIX.md5((string)$this->getBaseUrl()->withPath($path));
                }
            }
        }

        return $cacheKeys;
    }

    /**
     * Removes multiple items from the cache pool.
     *
     * @return boolean
     */
    protected function deleteCacheItems()
    {
        $cacheKeys = $this->getCacheItemKeys();

        return $this->cachePool()->deleteItems($cacheKeys);
    }



    // Dependencies
    // -------------------------------------------------------------------------

    /**
     * Retrieve the base URI of the application.
     *
     * @return UriInterface
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->baseUrl       = $container['base-url'];
        $this->defaultLocale = $container['locales/manager']->defaultLocale();
        $this->setCachePool($container['cache']);
    }
}
