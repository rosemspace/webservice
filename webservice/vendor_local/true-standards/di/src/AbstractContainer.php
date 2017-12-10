<?php

namespace True\Standards\DI;

use ArrayAccess;

/**
 * Abstract container with array functionality.
 */
abstract class AbstractContainer implements ContainerInterface, ArrayAccess
{
    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

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
        return $this->offsetExists($id);
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
        return isset($this->bindings[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $abstract The offset to retrieve
     *
     * @return mixed Can return all value types
     */
    public function offsetGet($abstract)
    {
        return $this->make($abstract);
    }

    /**
     * Offset to set.
     *
     * @param mixed $abstract The offset to assign the value to
     * @param mixed $concrete The value to set
     *
     * @throws \Exception
     */
    public function offsetSet($abstract, $concrete)
    {
        $this->bind($abstract, $concrete);
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->bindings[$offset]);
    }
}
