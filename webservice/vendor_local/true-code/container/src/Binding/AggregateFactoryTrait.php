<?php

namespace TrueCode\Container\Binding;

use InvalidArgumentException;

/**
 * Trait AggregateFactoryTrait.
 */
trait AggregateFactoryTrait
{
    protected function validateMethod(string $method)
    {
        if (! method_exists($this->getConcrete(), $method)) {
            throw new InvalidArgumentException("Method $method doesn't available from {$this->getConcrete()}.");
        }
    }

    protected function validateFunction(callable $function)
    {
        if (is_array($function)) {
            throw new InvalidArgumentException(
                "Attached callable parameter to {$this->getConcrete()} must be only a string or function, array given."
            );
        }
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregateBindingInterface
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->validateMethod($method);

        return (new MethodAggregateBinding($this->container, $this))->withMethodCall($method, $args);
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregateBindingInterface
     * @throws \ReflectionException
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        $this->validateFunction($function);

        return (new FunctionAggregateBinding($this->container, $this))->withFunctionCall($function, $args);
    }
}
