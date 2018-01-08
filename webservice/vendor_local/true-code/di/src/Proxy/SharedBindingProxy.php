<?php

namespace True\DI\Proxy;

use True\DI\Binding\BindingInterface;
use True\DI\Container;

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
     * @param array $args
     *
     * @return \True\DI\Binding\BindingInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getContext(array &$args) : BindingInterface
    {
        return $this->container->instance($this->abstract, parent::getContext($args)->make());
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
        $instance = $this->getContextWithCalls($args)->make();
        $this->container->instance($this->abstract, $instance)->commit();

        return $instance;
    }
}
