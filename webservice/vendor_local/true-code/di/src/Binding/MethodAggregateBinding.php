<?php

namespace True\DI\Binding;

use True\DI\ExtractorTrait;

class MethodAggregateBinding extends AbstractAggregateBinding
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

        // preserve temporary context which will be injected into all methods calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        foreach ($this->aggregate as $method) {
            $resolvedArgs = current($args) ?: [];
            $method->make($context, $resolvedArgs);
            next($args);
        }

        // replace preserved earlier temporary context by reverting original binding
        $this->container->set($this->getAbstract(), $this);

        return $context;
    }

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        $this->aggregate[$method] = new MethodBinding($this->container, $this->getConcrete(), $method, $args);

        return $this;
    }
}
