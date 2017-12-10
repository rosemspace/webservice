<?php

namespace True\DI\Proxies;

use TrueStandards\DI\{
    AbstractBinding, ContainerInterface
};

class BindingProxy extends AbstractBinding
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $abstract;

    public function __construct(ContainerInterface $container, $abstract, $concrete)
    {
        parent::__construct($concrete);

        $this->container = $container;
        $this->abstract = $abstract;
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
        $this->container->bind($this->abstract, $this->concrete);

        return $this->container->make($this->abstract, ...$args);
    }
}
