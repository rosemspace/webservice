<?php

namespace TrueStandards\DI;

/**
 * Representation of a container.
 */
interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    /**
     * Register a binding with the container.
     *
     * @param string $abstract // TODO: bind from array
     * @param mixed  $concrete
     *
     * @return BindingInterface
     * @throws ContainerExceptionInterface
     */
    public function bind(string $abstract, $concrete = null);

    /**
     * Register a non lazy binding with the container.
     *
     * @param string $abstract // TODO: bind from array
     * @param mixed  $concrete
     *
     * @return BindingInterface
     * @throws ContainerExceptionInterface
     */
    public function bindForce(string $abstract, $concrete = null);

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
    public function has($id): bool;

    /**
     * Make data from binding by abstract key.
     *
     * @param string  $abstract
     * @param array[] $args
     *
     * @return mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function make(string $abstract, array ...$args);

    /**
     * Bind data if not exist and make data from binding by abstract key.
     *
     * @param string  $abstract
     * @param array[] $args
     *
     * @return mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function makeForce(string $abstract, array ...$args);

    /**
     * Determine if a given type is shared.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isShared(string $abstract): bool;

    /**
     * Register an alias for existing interface.
     *
     * @param string $alias
     * @param string $abstract
     */
    public function alias(string $abstract, string $alias): void;

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract
     * @param mixed  $instance
     *
     * @return BindingInterface
     */
    public function instance(string $abstract, $instance);

    /**
     * Register a shared binding in the container.
     *
     * @param string|array $abstract
     * @param mixed        $concrete
     * @param array[]      $args
     *
     * @return BindingInterface
     */
    public function singleton(string $abstract, $concrete = null, array ...$args);

    /**
     * Register a singleton which can be reinitialized in the container.
     *
     * @param string|array $abstract
     * @param mixed        $concrete
     * @param array[]      $args
     *
     * @return BindingInterface
     */
    public function mutableSingleton(string $abstract, $concrete = null, array ...$args);
}
