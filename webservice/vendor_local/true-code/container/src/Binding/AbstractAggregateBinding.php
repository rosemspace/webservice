<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\AbstractContainer;

abstract class AbstractAggregateBinding implements BindingInterface
{
    use CallAggregateTrait;

    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var BindingInterface
     */
    protected $context;

    /**
     * @var BindingInterface[]|DependentBindingInterface[]
     */
    protected $aggregate;

    /**
     * AbstractAggregateBinding constructor.
     *
     * @param AbstractContainer $container
     * @param BindingInterface|CallAggregateTrait  $context
     */
    public function __construct(AbstractContainer $container, BindingInterface $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    public function commit() : BindingInterface
    {
        return $this->container->set($this->getAbstract(), $this);
    }

    public function getAbstract() : string
    {
        return $this->context->getAbstract();
    }

    public function getConcrete()
    {
        return $this->context->getConcrete();
    }
}
