<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\Binding\{
    AbstractAggregateBinding, AggregateBindingInterface, BindingInterface
};
use TrueCode\Container\ExtractorTrait;

class AggregateBindingProxy extends AbstractAggregateBinding implements AggregateBindingProxyInterface
{
    use ExtractorTrait;

    protected const METHOD = 0;

    protected const FUNCTION = 1;

    /**
     * @var BindingProxyInterface
     */
    protected $context;

    protected $aggregate = [[], []];

    protected $aggregateCommitted = [[], []];

    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[self::METHOD][$method] = $args;

        return $this;
    }

    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[self::FUNCTION][] = [$function, $args];

        return $this;
    }

    protected function resolveAggregate(BindingInterface $binding, array &$aggregate)
    {
        if ($aggregate[self::METHOD]) {
            foreach ($aggregate[self::METHOD] as $method => $args) {
                $binding = $binding->withMethodCall($method, $args);
            }
        }

        if ($aggregate[self::FUNCTION]) {
            foreach ($aggregate[self::FUNCTION] as [$function, $args]) {
                $binding = $binding->withFunctionCall($function, $args);
            }
        }

        return $binding;
    }

    public function resolve() : AggregateBindingInterface
    {
        $contextBinding = $this->context->resolve();
        $resolvedBinding = $this->resolveAggregate($contextBinding, $this->aggregateCommitted);

        if ($resolvedBinding !== $contextBinding) {
            $resolvedBinding->commit();
        }

        return $this->resolveAggregate($resolvedBinding, $this->aggregate);
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        return $this->resolve()->make(...$args);
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function call(array &...$args)
    {
        return $this->resolve()->call(...$args);
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function invoke(array &...$args)
    {
        return $this->resolve()->invoke(...$args);
    }

    public function commit() : BindingInterface
    {
        $this->aggregateCommitted = [
            self::METHOD   => array_merge($this->aggregateCommitted[self::METHOD], $this->aggregate[self::METHOD]),
            self::FUNCTION => array_merge($this->aggregateCommitted[self::FUNCTION], $this->aggregate[self::FUNCTION]),
        ];
        $this->aggregate = [[], []];

        return $this->container->set($this->getAbstract(), $this);
    }
}
