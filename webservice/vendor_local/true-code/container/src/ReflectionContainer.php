<?php

namespace TrueCode\Container;

use TrueCode\Container\Binding\{
    AggregateBindingInterface, FunctionBinding
};

class ReflectionContainer extends Container
{
    /**
     * Make with binding.
     *
     * @param string  $abstract
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function make(string $abstract, array ...$args)
    {
        if (! $this->has($abstract)) {
            if ($this->delegate) {
                return $this->delegate->make($abstract, ...$args);
            }

            return $this->forceBind($abstract)->make(...$args);
        }

        return $this->bindings[$abstract]->make(...$args);
    }

    /**
     * @param array|callable $callable
     * @param array[]        ...$args
     *
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     */
    public function call($callable, array ...$args)
    {
        if (is_array($callable)) {
            if (is_string(next($callable)) &&
                $abstract = (
                is_string(reset($callable))
                    ? reset($callable)
                    : (
                is_object(reset($callable))
                    ? get_class(reset($callable))
                    : false
                )
                )
            ) {
                if ($binding = $this->find($abstract)) {
                    return $binding->withMethodCall(next($callable))->call(...$args);
                }

                return $this->forceBindClass($abstract)->commit()
                    ->withMethodCall(next($callable))->call(...$args);
            }
        } elseif (is_callable($callable)) {
            if (is_string($callable)) {
                if ($binding = $this->find($callable)) {
                    if ($binding instanceof AggregateBindingInterface) {
                        return $binding->call(...$args);
                    } elseif ($binding instanceof FunctionBinding) {
                        return $binding->make(...$args);
                    } else {
                        throw new Exception\ContainerException("Binding $callable is not callable");
                    }
                }

                return $this->forceBindFunction($callable)->commit()->make(...$args);
            }

            return $this->forceBindFunction(__FUNCTION__, $callable)->make(...$args);
        }

        throw new Exception\ContainerException('Callable must be a binding name, function name,
        closure or an array of class name or instance and its method.');
    }
}
