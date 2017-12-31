<?php

namespace True\DI\Proxy;

use True\DI\{
    AbstractBinding, Container
};

class BindingProxy extends AbstractBinding
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $abstract;

    public function __construct(Container $container, $abstract, $concrete)
    {
        parent::__construct($concrete);

        $this->container = $container;
        $this->abstract = $abstract;
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

        return $this->container->make($this->abstract, ...$args);
    }
}
