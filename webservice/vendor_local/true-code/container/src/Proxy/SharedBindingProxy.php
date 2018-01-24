<?php

namespace TrueCode\Container\Proxy;

use TrueCode\Container\Binding\{
    BindingInterface, SharedBindingInterface
};
use TrueCode\Container\Container;

class SharedBindingProxy extends BindingProxy implements SharedBindingInterface
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
     * @return \TrueCode\Container\Binding\BindingInterface
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
