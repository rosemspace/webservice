<?php

namespace TrueCode\Container\Binding;

class FunctionAggregateBinding extends AbstractAggregateBinding
{
    use AggregateFactoryTrait;
    use AggregateProcessTrait;

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
        $this->container->instance($this->getAbstract(), $context)->commit();

        $this->makeAggregate($this->aggregateCommitted, $args, $result);
        $this->makeAggregate($this->aggregate, $args, $result);

        // replace preserved earlier temporary context by reverting original binding
        $this->container->set($this->getAbstract(), $this);

        return [$context, $result];
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregateBindingInterface
     * @throws \ReflectionException
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        $binding = new FunctionBinding($this->container, $this->getAbstract(), $function, $args);

        is_string($function)
            ? $this->aggregate[$function] = $binding
            : $this->aggregate[] = $binding;

        return $this;
    }
}
