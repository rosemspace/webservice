<?php

namespace TrueCode\Container\Binding;

class MethodAggregateBinding extends AbstractAggregateBinding
{
    use AggregateFactoryTrait, AggregateProcessTrait, AggregateTrait {
        AggregateFactoryTrait::withFunctionCall insteadof AggregateTrait;
        AggregateTrait::withMethodCall insteadof AggregateFactoryTrait;
    }

    protected function makeAggregate(array &$aggregate, $context, array &$args = [], &$result = null)
    {
        $localResult = null;

        foreach ($aggregate as $method) {
            $resolvedArgs = current($args) ?: [];
            $newResult = $method->make($context, $resolvedArgs);
            next($args);

            if (null !== $newResult) {
                $localResult = $newResult;
            }
        }

        if (null !== $localResult) {
            $result = $localResult;
        }
    }

    /**
     * @param $args
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function process(array &...$args) : array
    {
        $this->normalizeArgs($args);
        $context = $this->make(...$args);
        $result = null;

        // preserve temporary context which will be injected into all methods calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        $this->makeAggregate($this->aggregateCommitted, $context, $args, $result);
        $this->makeAggregate($this->aggregate, $context, $args, $result);

        // replace preserved earlier temporary context by reverting original binding
        $this->container->set($this->getAbstract(), $this);

        return [$context, $result];
    }
}
