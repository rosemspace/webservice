<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\ExtractorTrait;

class FunctionAggregateBinding extends AbstractAggregateBinding
{
    use ExtractorTrait;

    /**
     * @param array[] ...$args
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function invoke(array &...$args)
    {
        if (! isset($args[1])) {
            $args[] = $args[0];
        }

        $context = $this->context->make($this->extractFirst($args));
        $result = null;
        reset($args);

        // preserve temporary context which will be injected into all functions calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        foreach ($this->aggregate as $function) {
            $resolvedArgs = current($args) ?: [];
            $result = $function->make($resolvedArgs);
            next($args);
        }

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
        $this->aggregate[] = new FunctionBinding($this->container, $this->getAbstract(), $function, $args);

        return $this;
    }
}
