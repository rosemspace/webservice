<?php

namespace Rosem\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var DefinitionProxy[]
     */
    protected $definitions;

    protected $delegate;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        AbstractFacade::registerContainer($this);
    }

    public function set(string $id, $factory): void
    {
        $placeholder = &$this->definitions[$id];
        $placeholder = new DefinitionProxy($this, $placeholder, $factory);
    }

    public function extend(string $id, $factory): void
    {
        $this->definitions[$id]->extend($factory);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->definitions[$id]->get();
        }

        if ($this->delegate) {
            return $this->delegate->get($id);
        }

        throw new Exception\NotFoundException("$id definition not found.");
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
    public function has($id)
    {
        return isset($this->definitions[$id]);
    }
}
