<?php

namespace True\DI\Proxy;

use True\DI\{
    AbstractBinding, Binding\MethodAggregateBinding, BindingInterface
};

class BindingProxy extends AbstractBinding
{
    /**
     * @var array
     */
    protected $aggregate = [];

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        $this->aggregate[$method] = $args;

        return $this;
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
        $binding = $this->container->bindForce(
            $this->abstract,
            $this->concrete,
            ...$this->args ?: $args
        );

        if ($this->aggregate) {
            $this->container->set(
                $this->abstract,
                $binding = new MethodAggregateBinding($this->container, $binding, $this->aggregate)
            );
        }

        return $binding->make();
    }
}
