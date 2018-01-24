<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\AbstractContainer;

abstract class AbstractBinding implements BindingInterface
{
    use CallableAggregateTrait;

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

    public function __construct(AbstractContainer $container, string $abstract, $concrete = null, array $args = [])
    {
        $this->container = $container;
        $this->abstract = $abstract;
        $this->concrete = $concrete ?: $abstract;
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
