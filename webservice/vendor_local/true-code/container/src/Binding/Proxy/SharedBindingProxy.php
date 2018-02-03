<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\{
    Binding\SharedBindingInterface,
    Container
};

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
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        $instance = $this->resolve()->make(...$args);

        if (! $this->committed) {
            $this->container->instance($this->getAbstract(), $instance)->commit();
        }

        return $instance;
    }
}
