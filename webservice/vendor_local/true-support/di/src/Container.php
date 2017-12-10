<?php

namespace True\Support\DI;

use SplFixedArray;
use True\Standards\DI\AbstractContainer;
use True\Standards\DI\Bindings\BindingBuilder;
use True\Standards\DI\Bindings\BindingInterface;

class Container extends AbstractContainer
{
    /**
     * Container all bindings.
     *
     * @var BindingInterface[]
     */
    protected $bindings;

    public function __construct()
    {
        $this->bindings = [];
    }

    public function set($abstract, $concrete = null)
    {
        $this->bindings[$abstract] =
            new Proxies\BindingProxy($this->bindings, $abstract, $concrete ?: $abstract);
    }

    /**
     * Register a binding with the container.
     *
     * @param string $abstract // TODO: bind from array
     * @param mixed $concrete
     *
     * @throws \Exception
     */
    public function bind(string $abstract, $concrete)
    {
        // if $concrete is a string
        if (is_string($concrete)) { // TODO: move colon mark into constant
            if (count($explodedConcrete = explode('::', $concrete, 2)) > 1) {
                $this->bindings[$abstract] = new \True\Support\DI\Bindings\MethodBinding(
                    $this->bindings,
                    SplFixedArray::fromArray($explodedConcrete)
                );
            }

            // if concrete class represent an existed class
            if (class_exists($concrete)) {
                $this->bindings[$abstract] = method_exists($concrete, '__invoke')
                    ? new \True\Support\DI\Bindings\MethodBinding(
                        $this->bindings,
                        SplFixedArray::fromArray([$concrete, '__invoke'])
                    )
                    : new \True\Support\DI\Bindings\ClassBinding($this->bindings, $concrete);
            }
        }

        if (is_array($concrete)) {
            $this->bindings[$abstract] = new \True\Support\DI\Bindings\MethodBinding(
                $this->bindings,
                SplFixedArray::fromArray(
                    count($concrete) > 1 ? $concrete : [array_keys($concrete)[0], array_values($concrete)[0]]
                )
            );
        }

        // if $concrete is callable
        if (is_callable($concrete)) {
            $this->bindings[$abstract] = new \True\Support\DI\Bindings\CallableBinding($this->bindings, $concrete);
        }

        // if $concrete is an instance
        //return $this->getInstanceClosure($placeholder, $concrete);
        //return null;
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
            new Proxies\SharedBindingProxy($this->bindings, $abstract, $concrete ?: $abstract, [$args]);
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

    public function get($abstract)
    {
        return $this->bindings[$abstract]->make();
    }

    /**
     * Wrap function under makeInstance.
     *
     * @param string  $abstract
     * @param array[] $args
     *
     * @return mixed
     */
    public function make(string $abstract, array ...$args)
    {
        return $this->bindings[$abstract]->make(...$args);
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
