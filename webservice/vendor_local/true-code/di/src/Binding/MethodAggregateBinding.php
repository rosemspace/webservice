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

        foreach ($this->aggregate as $method) {
            $method->make($context, current($args) ?: []);
            next($args);
        }

        return $context;
    }

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        $this->aggregate[$method] = new MethodBinding($this->container, $this->getConcrete(), $method, $args);

        return $this;
    }
}
