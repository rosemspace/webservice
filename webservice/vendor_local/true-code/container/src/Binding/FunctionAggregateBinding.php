<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\ExtractorTrait;

class FunctionAggregateBinding extends AbstractAggregateBinding
{
    use ExtractorTrait,
        AggregateInstantiationTrait;

    protected function aggregateMake(array &$aggregate, array &$args = [], &$result = null)
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
    protected function invoke(array &...$args)
    {
        $this->normalizeInvokeArgs($args);
        $context = $this->context->make($this->extractFirst($args));
        $result = null;

        // preserve temporary context which will be injected into all functions calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        $this->aggregateMake($this->committedAggregate, $args, $result);
        $this->aggregateMake($this->aggregate, $args, $result);

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
