<?php

namespace True\DI\Binding;

use True\DI\AbstractContainer;

abstract class AbstractBinding implements BindingInterface
{
    use CallAggregateTrait;

    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var string
     */
    protected $abstract;

    /**
     * @var mixed
     */
    protected $concrete;

    /**
     * @var array
     */
    protected $args;

    public function __construct(AbstractContainer $container, string $abstract, $concrete, array $args = [])
    {
        $this->container = $container;
        $this->abstract = $abstract;
        $this->concrete = $concrete;
        $this->args = $args;
    }

    public function commit() : BindingInterface
    {
        return $this->container->set($this->getAbstract(), $this);
    }

    public function getAbstract() : string
    {
        return $this->abstract;
    }

    public function getConcrete()
    {
        return $this->concrete;
    }
}
