<?php

namespace TrueCode\Container\Binding;

/**
 * Trait AggregateFactoryTrait.
 */
trait AggregateFactoryTrait
{
    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregateBindingInterface
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
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
        return (new FunctionAggregateBinding($this->container, $this))->withFunctionCall($function, $args);
    }
}
