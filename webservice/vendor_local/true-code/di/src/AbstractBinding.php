<?php

namespace True\DI;

use True\DI\Binding\MethodAggregateBinding;

abstract class AbstractBinding implements BindingInterface
{
    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var string
     */
    protected $abstract;

    /**
     * @var string
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
        $container->set($abstract, $this);
    }

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        return $this->container->set(
            $this->abstract,
            (new MethodAggregateBinding($this->container, $this))->withMethodCall($method, $args)
        );
    }

    public function getConcrete() : string
    {
        return $this->concrete;
    }
}
