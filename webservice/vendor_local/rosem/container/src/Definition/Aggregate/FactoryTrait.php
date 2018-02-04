<?php

namespace Rosem\Container\Definition\Aggregate;

use InvalidArgumentException;

/**
 * Trait AggregateFactoryTrait.
 */
trait FactoryTrait
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
     * @return AggregatedDefinitionInterface
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregatedDefinitionInterface
    {
        $this->validateMethod($method);

        return (new MethodAggregatedDefinition($this->container, $this))->withMethodCall($method, $args);
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregatedDefinitionInterface
     * @throws \ReflectionException
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregatedDefinitionInterface
    {
        $this->validateFunction($function);

        return (new FunctionAggregatedDefinition($this->container, $this))->withFunctionCall($function, $args);
    }
}
