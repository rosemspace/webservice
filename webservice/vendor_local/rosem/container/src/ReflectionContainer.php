<?php

namespace Rosem\Container;

use Rosem\Container\Definition\{
    Aggregate\AggregatedDefinitionInterface,
    FunctionDefinition
};

class ReflectionContainer extends Container
{
    /**
     * Make with definition.
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

            return $this->defineNow($abstract)->make(...$args);
        }

        return $this->definitions[$abstract]->make(...$args);
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
                if ($definition = $this->find($abstract)) {
                    return $definition->withMethodCall(next($callable))->call(...$args);
                }

                return $this->defineClassNow($abstract)->commit()
                    ->withMethodCall(next($callable))->call(...$args);
            }
        } elseif (is_callable($callable)) {
            if (is_string($callable)) {
                if ($definition = $this->find($callable)) {
                    if ($definition instanceof AggregatedDefinitionInterface) {
                        return $definition->call(...$args);
                    } elseif ($definition instanceof FunctionDefinition) {
                        return $definition->make(...$args);
                    } else {
                        throw new Exception\ContainerException("Definition $callable is not callable");
                    }
                }

                return $this->defineFunctionNow($callable)->commit()->make(...$args);
            }

            return $this->defineFunctionNow(__FUNCTION__, $callable)->make(...$args);
        }

        throw new Exception\ContainerException('Callable must be a definition name, function name,
        closure or an array of class name or instance and its method.');
    }
}
