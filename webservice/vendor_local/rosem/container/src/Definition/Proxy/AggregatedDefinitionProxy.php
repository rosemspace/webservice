<?php

namespace Rosem\Container\Definition\Proxy;

use Rosem\Container\{
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

    protected function resolveAggregate(DefinitionInterface $definition, array &$aggregate)
    {
        if ($aggregate) {
            foreach ($aggregate as [$type, $callable, $args]) {
                $definition = $type === self::METHOD
                    ? $definition->withMethodCall($callable, $args)
                    : $definition->withFunctionCall($callable, $args);
            }
        }

        return $definition;
    }

    public function resolve() : AggregatedDefinitionInterface
    {
        $contextDefinition = $this->context->resolve();
        $resolvedDefinition = $this->resolveAggregate($contextDefinition, $this->aggregateCommitted);

        if ($resolvedDefinition !== $contextDefinition) {
            $resolvedDefinition->commit();
        }

        return $this->resolveAggregate($resolvedDefinition, $this->aggregate);
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
