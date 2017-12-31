<?php

namespace True\DI\Proxy;

use True\DI\Container;

class SharedBindingProxy extends BindingProxy
{
    /**
     * @var Container
     */
    protected $container;

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
        $this->container->bindForce($this->abstract, $this->concrete);
        $instance = $this->container->make($this->abstract, ...$this->args ?: $args);
        $this->container->instance($this->abstract, $instance);

        return $instance;
    }

    public function isShared() : bool
    {
        return true;
    }
}
