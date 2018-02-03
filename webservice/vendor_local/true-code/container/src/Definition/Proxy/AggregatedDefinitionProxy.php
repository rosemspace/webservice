<?php

namespace TrueCode\Container\Definition\Proxy;

use TrueCode\Container\{
    Definition\Aggregate\AbstractAggregatedDefinition,
    Definition\Aggregate\AggregatedDefinitionInterface,
    Definition\DefinitionInterface,
    ExtractorTrait
};

class AggregatedDefinitionProxy extends AbstractAggregatedDefinition implements AggregatedDefinitionProxyInterface
{
    use ExtractorTrait;

    protected const METHOD = 0;

    protected const FUNCTION = 1;

    /**
     * @var DefinitionProxyInterface
     */
    protected $context;

    public function withMethodCall(string $method, array $args = []) : AggregatedDefinitionInterface
    {
        $this->aggregate[] = [self::METHOD, $method, $args];

        return $this;
    }

    public function withFunctionCall(callable $function, array $args = []) : AggregatedDefinitionInterface
    {
        $this->aggregate[] = [self::FUNCTION, $function, $args];

        return $this;
    }

    protected function resolveAggregate(DefinitionInterface $binding, array &$aggregate)
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

    public function resolve() : AggregatedDefinitionInterface
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

    public function commit() : DefinitionInterface
    {
        $this->aggregateCommitted = array_merge($this->aggregateCommitted, $this->aggregate);
        $this->aggregate = [];

        return $this->container->set($this->getAbstract(), $this);
    }
}
