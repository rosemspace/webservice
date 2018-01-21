<?php

namespace True\DI\Binding;

use True\DI\ExtractorTrait;

class FunctionAggregateBinding extends AbstractAggregateBinding
{
    use ExtractorTrait;

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        $context = $this->context->make($this->extractFirst($args));
        reset($args);

        // preserve temporary context which will be injected into all functions calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        foreach ($this->aggregate as $function) {
            $resolvedArgs = current($args) ?: [];
            $function->make($resolvedArgs);
            next($args);
        }

        // replace preserved earlier temporary context by reverting original binding
        $this->container->set($this->getAbstract(), $this);

        return $context;
    }

    public function withFunctionCall(callable $function, array $args = []) : BindingInterface
    {
        $this->aggregate[] = new FunctionBinding($this->container, $this->getAbstract(), $function, $args);

        return $this;
    }
}
