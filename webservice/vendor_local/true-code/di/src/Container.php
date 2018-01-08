<?php

namespace True\DI;

use True\DI\Binding\{
    BindingInterface, ClassBinding, FunctionBinding, SharedBinding
};
use True\DI\Exception\NotFoundException;
use True\DI\Proxy\SharedBindingProxy;

class Container extends AbstractContainer
{
    use ExtractorTrait;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        AbstractFacade::registerContainer($this);
    }

    public function bind(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        return new Proxy\BindingProxy($this, $abstract, $concrete ?: $abstract, $args);
    }

    public function bindForce(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        if (! $concrete) {
            $concrete = $abstract;
        }

        if (is_string($concrete) && class_exists($concrete)) {
            $binding = new ClassBinding($this, $abstract, $concrete, $this->extractFirst($args));

            return method_exists($concrete, '__invoke')
                ? $binding->withMethodCall('__invoke', $args ?: [])
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
        return $this->find($abstract)->make(...$args);
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
        return new SharedBinding($this, $abstract, $instance);
    }

    public function share(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        return new Proxy\SharedBindingProxy($this, $abstract, $concrete ?: $abstract, $args);
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
