<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use ArrayAccess;
use Countable;
use Psr\Container\{
    ContainerExceptionInterface,
    ContainerInterface,
    NotFoundExceptionInterface
};
use Rosem\Component\Container\Exception;

/**
 * Class AbstractContainer.
 *
 * @package Rosem\Component\Container
 */
abstract class AbstractContainer implements ContainerInterface, ArrayAccess, Countable
{
    use ConfigFileTrait;

    /**
     * @var mixed[]|Definition[]
     */
    protected iterable $definitions;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $child;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $parent;

    /**
     * AbstractContainer constructor.
     *
     * @param array $definitions
     */
    protected function __construct(iterable $definitions = [])
    {
        $this->definitions = $definitions;
    }

    /**
     * Set value factory by id.
     *
     * @param string $id
     * @param mixed  $factory
     */
    abstract protected function set(string $id, $factory): void;

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->definitions[$id];
        }

        if ($this->child !== null) {
            return $this->child->get($id);
        }

        throw Exception\NotFoundException::dueToMissingEntry($id);
    }

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
    public function has($id): bool
    {
        return isset($this->definitions[$id]);
    }

    public function delegate(self $delegate): void
    {
        $this->child = $delegate;
        $this->child->parent = $this;
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $id An offset to check for
     *
     * @return bool true on success or false on failure
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($id)
    {
        return $this->has($id);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $id The offset to retrieve
     *
     * @return mixed Can return all value types
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function offsetGet($id)
    {
        return $this->get($id);
    }

    /**
     * Offset to set.
     *
     * @param mixed $id      The offset to assign the value to
     * @param mixed $factory The value to set
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function offsetSet($id, $factory)
    {
        $this->set($id, $factory);
    }

    /**
     * Offset to unset.
     *
     * @param mixed $id The offset to unset
     *
     * @return void
     */
    public function offsetUnset($id)
    {
        unset($this->definitions[$id]);
    }

    /**
     * Count elements of an object.
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->definitions);
    }
}
