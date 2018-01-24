<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\ExtractorTrait;

class MethodAggregateBinding extends AbstractAggregateBinding
{
    use ExtractorTrait,
        AggregateInstantiationTrait;

    protected function aggregateMake(array &$aggregate, $context, array &$args = [], &$result = null)
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
    protected function invoke(array &...$args)
    {
        $this->normalizeInvokeArgs($args);
        $context = $this->context->make($this->extractFirst($args));
        $result = null;

        // preserve temporary context which will be injected into all methods calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        $this->aggregateMake($this->committedAggregate, $context, $args, $result);
        $this->aggregateMake($this->aggregate, $context, $args, $result);

        // replace preserved earlier temporary context by reverting original binding
        $this->container->set($this->getAbstract(), $this);

        return [$context, $result];
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregateBindingInterface
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[$method] = new MethodBinding($this->container, $this->getConcrete(), $method, $args);

        return $this;
    }
}
