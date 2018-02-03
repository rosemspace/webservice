<?php

namespace TrueCode\Container\Binding;

/**
 * Trait AggregateTrait.
 */
trait AggregateTrait
{
    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregateBindingInterface|self
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->validateMethod($method);

        $this->aggregate[$method] = new MethodBinding($this->container, $this->getConcrete(), $method, $args);

        return $this;
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregateBindingInterface|self
     * @throws \ReflectionException
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        $this->validateFunction($function);

        $binding = new FunctionBinding($this->container, $this->getAbstract(), $function, $args);

        is_string($function)
            ? $this->aggregate[$function] = $binding
            : $this->aggregate[] = $binding;

        return $this;
    }
}
