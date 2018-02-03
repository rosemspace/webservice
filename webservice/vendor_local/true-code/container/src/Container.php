<?php

namespace TrueCode\Container;

use TrueCode\Container\Binding\{
    AggregateBindingInterface, BindingInterface, ClassBinding, FunctionBinding, SharedBinding, SharedBindingInterface, Proxy\BindingProxyInterface
};

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

    public function bind(string $abstract, $concrete = null, array ...$args) : BindingProxyInterface
    {
        return new Binding\Proxy\BindingProxy($this, $abstract, $concrete ?: $abstract, $args);
    }

    /**
     * @param string  $abstract
     * @param null    $concrete
     * @param array[] ...$args
     *
     * @return BindingInterface
     * @throws \ReflectionException
     */
    public function forceBind(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        if (! $concrete) {
            $concrete = $abstract;
        }

        if (is_string($concrete) && class_exists($concrete)) {
            return $this->forceBindClass($abstract, $concrete, ...$args);
        }

        if (is_callable($concrete)) {
            return $this->forceBindFunction($abstract, $concrete, reset($args) ?: []);
        }

        return new SharedBinding($this, $abstract, $concrete, reset($args) ?: []);
    }

    /**
     * @param string  $abstract
     * @param null    $concrete
     * @param array[] ...$args
     *
     * @return BindingInterface
     * @throws \ReflectionException
     */
    protected function forceBindClass(string $abstract, $concrete = null, array ...$args) : BindingInterface
    {
        $binding = new ClassBinding($this, $abstract, $concrete, $this->extractFirst($args));

        return method_exists($concrete, '__invoke')
            ? $binding->withMethodCall('__invoke', $args ?: [])
            : $binding;
    }

    /**
     * @param string $abstract
     * @param null   $concrete
     * @param array  $args
     *
     * @return BindingInterface
     * @throws \ReflectionException
     */
    protected function forceBindFunction(string $abstract, $concrete = null, array $args = []) : BindingInterface
    {
        return new FunctionBinding($this, $abstract, $concrete, $args);
    }

    /**
     * Wrap function under makeInstance.
     *
     * @param string  $abstract
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function make(string $abstract, array ...$args)
    {
        if (! $binding = $this->find($abstract)) {
            if ($this->delegate) {
                return $this->delegate->make($abstract, ...$args);
            }

            throw new Exception\NotFoundException("$abstract binding not found.");
        }

        return $binding->make(...$args);
    }

    public function invoke($abstract, array ...$args)
    {
        // TODO: improve - make like call method
        if (! $binding = $this->find($abstract)) {
            if ($this->delegate) {
                return $this->delegate->invoke($abstract, ...$args);
            }

            throw new Exception\NotFoundException("$abstract binding not found.");
        }

        return $binding instanceof AggregateBindingInterface
            ? $binding->invoke(...$args)
            : $binding->make(...$args);
    }

    /**
     * @param array|callable $callable
     * @param array[]        ...$args
     *
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function call($callable, array ...$args)
    {
        $notFound = false;

        if (is_array($callable)) {
            if (is_string(next($callable))) {
                if (is_string(reset($callable))) {
                    if ($binding = $this->find(reset($callable))) {
                        return $binding->withMethodCall(next($callable))->call(...$args);
                    } else {
                        $notFound = true;
                    }
                } elseif (is_object(reset($callable))) {
                    $abstract = get_class(reset($callable));

                    if ($binding = $this->find($abstract)) {
                        return $binding->withMethodCall(next($callable))->call(...$args);
                    } else {
                        $notFound = true;
                    }
                }
            } else {
                throw new Exception\ContainerException(
                    'Callable array must represent class name or instance and its method'
                );
            }
        } elseif (is_string($callable) && is_callable($callable)) {
            // TODO: add support for "::"
            //is a class "classname::method"
            if (strpos($callable, '::') === false) {
                $class = $callable;
                $method = '__invoke';
            } else {
                [$class, $method] = explode('::', $callable, 2);
            }

            if ($binding = $this->find($callable)) {
                if ($binding instanceof AggregateBindingInterface) {
                    return $binding->call(...$args);
                } elseif ($binding instanceof FunctionBinding) {
                    return $binding->make(...$args);
                } else {
                    throw new Exception\ContainerException("Binding $callable is not callable");
                }
            } else {
                $notFound = true;
            }
        }

        if ($this->delegate) {
            return $this->delegate->call($callable, ...$args);
        } elseif ($notFound) {
            throw new Exception\NotFoundException('Callable binding not found.');
        }

        throw new Exception\ContainerException(
            'Callable must be a function name or an array of class name or instance and its method'
        );
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
        return $this->invoke($abstract);
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

    public function share(string $abstract, $concrete = null, array ...$args) : BindingProxyInterface
    {
        return new Binding\Proxy\SharedBindingProxy($this, $abstract, $concrete ?: $abstract, $args);
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
            : $this->bindings[$abstract] instanceof SharedBindingInterface;
    }
}
