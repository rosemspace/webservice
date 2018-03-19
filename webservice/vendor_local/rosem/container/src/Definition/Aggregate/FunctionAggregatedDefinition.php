<?php

namespace Rosem\Container\Definition\Aggregate;

use Rosem\Container\Definition\SharedDefinition;

class FunctionAggregatedDefinition extends AbstractAggregatedDefinition
{
    use CollectorTrait,
        FactoryTrait,
        ProcessTrait {
        CollectorTrait::withFunctionCall insteadof FactoryTrait;
        FactoryTrait::withMethodCall insteadof CollectorTrait;
    }

    protected function makeAggregate(array &$aggregate, array &$args = [], &$result = null)
    {
        $localResult = null;

        foreach ($aggregate as $function) {
            $resolvedArgs = current($args) ?: [];
            $newResult = $function->make($resolvedArgs);
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
     * @param array[] ...$args
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

        // preserve temporary context which will be injected into all functions calls
        $this->container->set($this->getAbstract(), new SharedDefinition($this->container, $this->getAbstract(), $context))->commit();

        $this->makeAggregate($this->aggregateCommitted, $args, $result);
        $this->makeAggregate($this->aggregate, $args, $result);

        // replace preserved earlier temporary context by reverting original definition
        $this->container->set($this->getAbstract(), $this);

        return [$context, $result];
    }
}
