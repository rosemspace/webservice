<?php

namespace TrueCode\Container\Definition\Proxy;

use TrueCode\Container\{
    Definition\SharedDefinitionInterface,
    Container
};

class SharedDefinitionProxy extends DefinitionProxy implements SharedDefinitionInterface
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
