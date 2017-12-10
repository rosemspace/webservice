<?php

namespace True\DI;

use SplFixedArray;
use TrueStandards\DI\AbstractContainer;
use True\DI\Bindings\{
    ClassBinding, FunctionBinding, MethodBinding
};
use True\DI\Exceptions\{
    ContainerException, NotFoundException
};

class Container extends AbstractContainer
{
    /**
     * Container all bindings.
     *
     * @var \TrueStandards\DI\BindingInterface[]
     */
    protected $bindings;

    public function __construct()
    {
        $this->bindings = [];
    }

    public function set($abstract, $concrete = null)
    {
        $this->bindings[$abstract] =
            new Proxies\BindingProxy($this, $abstract, $concrete ?: $abstract);
    }

    /**
     * Register a binding with the container.
     *
     * @param string $abstract // TODO: bind from array
     * @param mixed  $concrete
     *
     * @return \TrueStandards\DI\BindingInterface
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     */
    public function bind(string $abstract, $concrete)
    {
        // if $concrete is a string
        if (is_string($concrete)) { // TODO: move colon mark into constant
            if (count($explodedConcrete = explode('::', $concrete, 2)) > 1) {
                return $this->bindings[$abstract] = new MethodBinding(
                    $this,
                    SplFixedArray::fromArray($explodedConcrete)
                );
            }

            // if concrete class represent an existed class
            if (class_exists($concrete)) {
                return $this->bindings[$abstract] = method_exists($concrete, '__invoke')
                    ? new MethodBinding(
                        $this,
                        SplFixedArray::fromArray([$concrete, '__invoke'])
                    )
                    : new ClassBinding($this, $concrete);
            }
        }

        if (is_array($concrete)) {
            return $this->bindings[$abstract] = new MethodBinding(
                $this,
                SplFixedArray::fromArray(
                    count($concrete) > 1 ? $concrete : [array_keys($concrete)[0], array_values($concrete)[0]]
                )
            );
        }

        // if $concrete is callable
        if (is_callable($concrete)) {
            return $this->bindings[$abstract] = new FunctionBinding($this, $concrete);
        }

        // if $concrete is an instance
        //return $this->getInstanceClosure($placeholder, $concrete);
        //return null;
        throw new ContainerException('Cannot bind');
    }

    /**
     * Register an alias for existing interface.
     *
     * @param string $alias
     * @param string $abstract
     */
    public function alias(string $abstract, string $alias)
    {
        $this->bindings[$alias] = &$this->bindings[$abstract];
    }

    public function instance(string $abstract, $instance)
    {
        $this->bindings[$abstract] = new Bindings\SharedBinding($instance);
    }

    public function singleton(string $abstract, $concrete = null, array $args = [])
    {
        $this->bindings[$abstract] =
            new Proxies\SharedBindingProxy($this, $abstract, $concrete ?: $abstract, [$args]);
    }

    /**
     * Register a singleton which can be reinitialized in the container.
     *
     * @param string|array $abstract
     * @param mixed        $concrete
     * @param array        $args
     */
    public function mutableSingleton(string $abstract, $concrete = null, array $args = [])
    {
        // TODO: Implement mutableSingleton() method.
    }

    /**
     * @param string $abstract
     *
     * @return mixed
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     * @throws \TrueStandards\DI\NotFoundExceptionInterface
     */
    public function get($abstract)
    {
        return $this->make($abstract);
    }

    /**
     * Wrap function under makeInstance.
     *
     * @param string  $abstract
     * @param array[] $args
     *
     * @return mixed
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     * @throws \TrueStandards\DI\NotFoundExceptionInterface
     */
    public function make(string $abstract, array ...$args)
    {
        if ($this->has($abstract)) {
            return $this->bindings[$abstract]->make(...$args);
        }

        throw new NotFoundException("$abstract binding not found.");
    }

    /**
     * Determine if a given type is shared.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isShared(string $abstract) : bool
    {
        return $this->bindings[$abstract]->isShared();
    }
}
