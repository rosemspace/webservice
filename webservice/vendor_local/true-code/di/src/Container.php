<?php

namespace True\DI;

use SplFixedArray;
use TrueStandards\DI\AbstractContainer;
use True\DI\Bindings\{
    ClassBinding, FunctionBinding, MethodBinding
};
use True\DI\Exceptions\NotFoundException;
use TrueStandards\DI\AbstractFacade;

class Container extends AbstractContainer
{
    public function __construct()
    {
        AbstractFacade::registerContainer($this);
    }

    public function bind(string $abstract, $concrete = null)
    {
        return $this->bindings[$abstract] =
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
    public function bindForce(string $abstract, $concrete = null)
    {
        if (! $concrete) {
            $concrete = $abstract;
        }

        if (is_string($concrete)) {
            if (count($explodedConcrete = explode(static::CLASS_METHOD_SEPARATOR, $concrete, 2)) > 1 &&
                method_exists($explodedConcrete[0], $explodedConcrete[1])
            ) {
                return $this->bindings[$abstract] = new MethodBinding(
                    $this,
                    SplFixedArray::fromArray($explodedConcrete)
                );
            }

            if (class_exists($concrete)) {
                return $this->bindings[$abstract] = method_exists($concrete, '__invoke')
                    ? new MethodBinding($this, SplFixedArray::fromArray([$concrete, '__invoke']))
                    : new ClassBinding($this, $concrete);
            }
        }

        if (is_array($concrete)) {
            if (count($concrete) == 2 && method_exists($concrete[0], $concrete[1])) {
                return $this->bindings[$abstract] =
                    new MethodBinding($this, SplFixedArray::fromArray($concrete));
            } else {
                if (count($concrete) == 1 && method_exists(
                        $class = array_keys($concrete)[0],
                        $method = array_values($concrete)[0]
                    )
                ) {
                    return $this->bindings[$abstract] =
                        new MethodBinding($this, SplFixedArray::fromArray([$class, $method]));
                }
            }
        }

        if (is_callable($concrete)) {
            return $this->bindings[$abstract] = new FunctionBinding($this, $concrete);
        }

        return $this->bindings[$abstract] = new Bindings\SharedBinding($concrete);
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

    public function singleton(string $abstract, $concrete = null, array ...$args)
    {
        $this->bindings[$abstract] =
            new Proxies\SharedBindingProxy($this, $abstract, $concrete ?: $abstract, $args);
    }

    /**
     * Register a singleton which can be reinitialized in the container.
     *
     * @param string|array $abstract
     * @param mixed        $concrete
     * @param array[]      $args
     */
    public function mutableSingleton(string $abstract, $concrete = null, array ...$args)
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
     * Make with binding.
     *
     * @param string  $abstract
     * @param array[] $args
     *
     * @return mixed
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     */
    public function makeForce(string $abstract, array ...$args)
    {
        if (! $this->has($abstract)) {
            $this->bindForce($abstract);
        }

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
