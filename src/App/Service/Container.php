<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\Container\AliasedServiceException;
use Pimple\Container as PimpleContainer;
use Pimple\Exception\UnknownIdentifierException;

/**
 * App Service Container
 *
 * Provides support for aliases.
 *
 * As is, this class can not be used as an Slim application container.
 */
class Container extends PimpleContainer
{
    /**
     * The registered type aliases.
     *
     * @var array<string, string>
     */
    protected $aliases = [];

    /**
     * Determine if a given string is an alias.
     *
     * @param  string $name The unique identifier to lookup.
     * @return bool
     */
    public function isAlias(string $name) : bool
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Alias an object definition to a different name.
     *
     * @param  string $id    The original identifier for the object.
     * @param  string $alias The alias identifier for the object.
     * @throws UnknownIdentifierException If the identifier is not defined.
     * @return void
     */
    public function alias(string $id, string $alias) : void
    {
        if (!isset($this[$id])) {
            throw new UnknownIdentifierException($id);
        }

        $this->aliases[$alias] = $id;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id    The unique identifier for the parameter or object.
     * @param  mixed  $value The value of the parameter or a closure to define an object.
     * @throws AliasedServiceException If the identifier is an alias.
     * @return void
     */
    public function offsetSet($id, $value)
    {
        if (isset($this->aliases[$id])) {
            throw new AliasedServiceException($id);
        }

        parent::offsetSet($id, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id The unique identifier for the parameter or object.
     * @return mixed The value of the parameter or an object.
     */
    public function offsetGet($id)
    {
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }

        return parent::offsetGet($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id The unique identifier for the parameter or object.
     * @return bool
     */
    public function offsetExists($id)
    {
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }

        return parent::offsetExists($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id The unique identifier for the parameter or object.
     * @return void
     */
    public function offsetUnset($id)
    {
        if (isset($this->aliases[$id])) {
            $alias = $id;
            $id    = $this->aliases[$id];
            unset($this->aliases[$alias]);
        }

        parent::offsetUnset($id);
    }
}
