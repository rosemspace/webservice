<?php

namespace True\DI\Proxies;

use TrueStandards\DI\ContainerInterface;

class SharedBindingProxy extends BindingProxy
{
    /**
     * @var array
     */
    protected $args;

    public function __construct(ContainerInterface $container, $abstract, $concrete, $args)
    {
        parent::__construct($container, $abstract, $concrete);

        $this->args = $args;
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     * @throws \TrueStandards\DI\NotFoundExceptionInterface
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
