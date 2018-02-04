<?php

namespace Rosem\Container\Definition\Aggregate;

use Rosem\Container\Definition\FunctionDefinition;

/**
 * Trait AggregateTrait.
 */
trait CollectorTrait
{
    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregatedDefinitionInterface|self
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregatedDefinitionInterface
    {
        $this->validateMethod($method);

        $this->aggregate[$method] = new MethodInvoker($this->container, $this->getConcrete(), $method, $args);

        return $this;
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregatedDefinitionInterface|self
     * @throws \ReflectionException
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregatedDefinitionInterface
    {
        $this->validateFunction($function);

        $binding = new FunctionDefinition($this->container, $this->getAbstract(), $function, $args);

        is_string($function)
            ? $this->aggregate[$function] = $binding
            : $this->aggregate[] = $binding;

        return $this;
    }
}
