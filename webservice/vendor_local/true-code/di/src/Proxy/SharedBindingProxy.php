<?php

namespace True\DI\Proxy;

use True\DI\Container;
use True\DI\Binding\MethodAggregateBinding;

class SharedBindingProxy extends BindingProxy
{
    /**
     * @var array
     */
    protected $args;

    public function __construct(Container $container, $abstract, $concrete, $args)
    {
        parent::__construct($container, $abstract, $concrete);

        $this->args = $args;
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
        $resolvedArgs = $args ?: $this->args;
        $binding = $this->container->bindForce($this->abstract, $this->concrete);

        if ($this->aggregate) {
            $binding = new MethodAggregateBinding($this->container, $binding, $this->aggregate);
        }

        $instance = $binding->make($resolvedArgs);
        $this->container->instance($this->abstract, $instance);

        return $instance;
    }
}
