<?php

namespace True\DI\Binding;

use True\DI\AbstractContainer;

/**
 * Trait CallAggregateTrait.
 */
trait CallAggregateTrait
{
    /**
     * @var AbstractContainer
     */
    protected $container;

    abstract public function getAbstract() : string;

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        return (new MethodAggregateBinding($this->container, $this))->withMethodCall($method, $args);
    }

    public function withFunctionCall(callable $function, array $args = []) : BindingInterface
    {
        return (new FunctionAggregateBinding($this->container, $this))->withFunctionCall($function, $args);
    }
}
