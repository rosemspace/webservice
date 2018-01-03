<?php

namespace True\DI;

use SplFixedArray;
use True\DI\Binding\{
    ClassBinding, FunctionBinding, SharedBinding
};
use True\DI\Exception\NotFoundException;
use True\DI\Proxy\SharedBindingProxy;

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

    public function bind(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        return $this->bindings[$abstract] =
            new Proxy\BindingProxy($this, $abstract, $concrete ?: $abstract, $args);
    }

    public function bindForce(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        if (! $concrete) {
            $concrete = $abstract;
        }

        if (class_exists($concrete)) {
            $constructArgs = reset($args) ?: [];

            if (count($args) > 1) {
                unset($args[key($args)]);
                $methodArgs = $args;
            } else {
                $methodArgs = $constructArgs;
                $constructArgs = [];
            }

            $binding = new ClassBinding($this, $abstract, $concrete, $constructArgs);

            return method_exists($concrete, '__invoke')
                ? $binding->withMethodCall('__invoke', $methodArgs)
                : $binding;
        }

        if (is_callable($concrete)) {
            return new FunctionBinding($this, $abstract, $concrete, reset($args) ?: []);
        }

        return new SharedBinding($this, $abstract, $concrete, reset($args) ?: []);
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
        return $this->bindings[$abstract] = new SharedBinding($this, $abstract, $instance);
    }

    public function share(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        return $this->bindings[$abstract] =
            new Proxy\SharedBindingProxy($this, $abstract, $concrete ?: $abstract, $args);
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
            : (
                $this->bindings[$abstract] instanceof SharedBinding ||
                $this->bindings[$abstract] instanceof SharedBindingProxy
            );
    }
}
