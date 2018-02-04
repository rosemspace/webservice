<?php

namespace Rosem\Container;

use ArrayAccess;
use Psr\Container\ContainerInterface;
use Rosem\Container\Definition\{
    Aggregate\AggregatedDefinitionInterface,
    DefinitionInterface,
    Proxy\DefinitionProxyInterface
};

/**
 * Abstract container with array functionality.
 */
abstract class AbstractContainer implements ContainerInterface, ArrayAccess
{
    /**
     * Symbols for separate class name and method name.
     */
    protected const CLASS_METHOD_SEPARATOR = '::';

    /**
     * The container's definitions.
     *
     * @var DefinitionInterface[]
     */
    protected $definitions = [];

    /**
     * @var self
     */
    protected $delegate;

    public function set(string $abstract, DefinitionInterface $definition) : DefinitionInterface
    {
        return $this->definitions[$abstract] = $definition;
    }

    /**
     * @param string $abstract
     *
     * @return DefinitionInterface|AggregatedDefinitionInterface|null
     * @throws Exception\NotFoundException
     */
    public function find(string $abstract) : ?DefinitionInterface
    {
        if ($this->has($abstract)) {
            return $this->definitions[$abstract];
        }

        if ($this->delegate) {
            return $this->delegate->find($abstract);
        }

        return null;
    }

    public function delegate(self $container)
    {
        $this->delegate = $container;
    }

    abstract public function define(string $abstract, $concrete = null, array ...$args) : DefinitionProxyInterface;

    abstract public function defineNow(string $abstract, $concrete = null, array ...$args) : DefinitionInterface;

    abstract public function share(string $abstract, $concrete = null, array ...$args) : DefinitionProxyInterface;

    abstract public function instance(string $abstract, $instance) : DefinitionInterface;

    abstract public function make(string $abstract, array ...$args);

    abstract public function invoke($abstract, array ...$args);

    abstract public function call($callable, array ...$args);

    abstract public function isShared(string $abstract) : bool;

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id) : bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset An offset to check for
     *
     * @return boolean true on success or false on failure
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $abstract The offset to retrieve
     *
     * @return mixed Can return all value types
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function offsetGet($abstract)
    {
        return $this->get($abstract);
    }

    /**
     * Offset to set.
     *
     * @param mixed $abstract The offset to assign the value to
     * @param mixed $concrete The value to set
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function offsetSet($abstract, $concrete)
    {
        $this->define($abstract, $concrete);
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->definitions[$offset]);
    }
}
