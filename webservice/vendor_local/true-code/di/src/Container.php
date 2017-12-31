<?php

namespace True\DI;

use SplFixedArray;
use True\DI\Binding\{
    ClassBinding, FunctionBinding, MethodBinding, SharedBinding
};
use True\DI\Exception\NotFoundException;

class Container extends AbstractContainer
{
    /**
     * @var self
     */
    protected $delegate;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        AbstractFacade::registerContainer($this);
    }

    public function delegate(self $container)
    {
        $this->delegate = $container;
    }

    public function bind(string $abstract, $concrete = null) : BindingInterface
    {
        return $this->bindings[$abstract] =
            new Proxy\BindingProxy($this, $abstract, $concrete ?: $abstract);
    }

    /**
     * Register a binding with the container.
     *
     * @param string $abstract
     * @param mixed  $concrete
     *
     * @return BindingInterface
     */
    public function bindForce(string $abstract, $concrete = null) : BindingInterface
    {
        if (! $concrete) {
            $concrete = $abstract;
        }

        if (is_string($concrete)) {
            if (class_exists($concrete)) {
                return $this->bindings[$abstract] = method_exists($concrete, '__invoke')
                    ? new MethodBinding($this, SplFixedArray::fromArray([$concrete, '__invoke']))
                    : new ClassBinding($this, $concrete);
            }

            if (count($explodedConcrete = explode(static::CLASS_METHOD_SEPARATOR, $concrete, 2)) > 1 &&
                method_exists($explodedConcrete[0], $explodedConcrete[1])
            ) {
                return $this->bindings[$abstract] = new MethodBinding(
                    $this,
                    SplFixedArray::fromArray($explodedConcrete)
                );
            }
        } elseif (is_array($concrete)) {
            if (count($concrete) == 2 && method_exists($concrete[0], $concrete[1])) {
                return $this->bindings[$abstract] =
                    new MethodBinding($this, SplFixedArray::fromArray($concrete));
            } elseif (
                count($concrete) == 1 &&
                method_exists(
                    $class = array_keys($concrete)[0],
                    $method = array_values($concrete)[0]
                )
            ) {
                return $this->bindings[$abstract] =
                    new MethodBinding($this, SplFixedArray::fromArray([$class, $method]));
            }
        } elseif (method_exists($concrete, '__invoke')) {
            return $this->bindings[$abstract] =
                new MethodBinding($this, SplFixedArray::fromArray([$concrete, '__invoke']));
        } elseif (is_callable($concrete)) {
            return $this->bindings[$abstract] = new FunctionBinding($this, $concrete);
        }

        return $this->bindings[$abstract] = new Binding\SharedBinding($concrete);
    }

    /**
     * Wrap function under makeInstance.
     *
     * @param string  $abstract
     * @param array[] ...$args
     *
     * @return mixed
     * @throws NotFoundException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(string $abstract, array ...$args)
    {
        if ($this->has($abstract)) {
            return $this->bindings[$abstract]->make(...$args);
        }

        if ($this->delegate) {
            return $this->delegate->make($abstract, ...$args);
        }

        throw new NotFoundException("$abstract binding not found.");
    }

    /**
     * @param string $abstract
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get($abstract)
    {
        return $this->make($abstract);
    }

    /**
     * Register an alias for existing interface.
     *
     * @param string $alias
     * @param string $abstract
     */
    public function alias(string $abstract, string $alias) : void
    {
        $this->bindings[$alias] = &$this->bindings[$abstract];
    }

    public function instance(string $abstract, $instance) : BindingInterface
    {
        return $this->bindings[$abstract] = new SharedBinding($instance);
    }

    public function share(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        return $this->bindings[$abstract] =
            new Proxy\SharedBindingProxy($this, $abstract, $concrete ?: $abstract, $args);
    }

    /**
     * Register a shared concrete which can be reinitialized in the container.
     *
     * @param string|array $abstract
     * @param mixed        $concrete
     * @param array[]      $args
     *
     * @return BindingInterface
     */
    public function mutable(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        // TODO: Implement mutableSingleton() method.

        return $this->instance($abstract, $concrete);
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
        return ($this->delegate && ! $this->has($abstract))
            ? $this->delegate->isShared($abstract)
            : $this->bindings[$abstract]->isShared();
    }
}
