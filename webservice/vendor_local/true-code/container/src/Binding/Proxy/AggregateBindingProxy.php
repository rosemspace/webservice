<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\{
    Binding\AbstractAggregateBinding,
    Binding\AggregateBindingInterface,
    Binding\BindingInterface,
    ExtractorTrait
};

class AggregateBindingProxy extends AbstractAggregateBinding implements AggregateBindingProxyInterface
{
    use ExtractorTrait;

    protected const METHOD = 0;

    protected const FUNCTION = 1;

    /**
     * @var BindingProxyInterface
     */
    protected $context;

    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[] = [self::METHOD, $method, $args];

        return $this;
    }

    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[] = [self::FUNCTION, $function, $args];

        return $this;
    }

    protected function resolveAggregate(BindingInterface $binding, array &$aggregate)
    {
        if ($aggregate) {
            foreach ($aggregate as [$type, $callable, $args]) {
                $binding = $type === self::METHOD
                    ? $binding->withMethodCall($callable, $args)
                    : $binding->withFunctionCall($callable, $args);
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
        $this->aggregateCommitted = array_merge($this->aggregateCommitted, $this->aggregate);
        $this->aggregate = [];

        return $this->container->set($this->getAbstract(), $this);
    }
}
