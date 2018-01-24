<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\AbstractContainer;

abstract class AbstractAggregateBinding implements AggregateBindingInterface
{
    use CallableAggregateTrait;

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
    protected $aggregate = [];

    /**
     * @var BindingInterface[]|DependentBindingInterface[]
     */
    protected $committedAggregate = [];

    /**
     * AbstractAggregateBinding constructor.
     *
     * @param AbstractContainer $container
     * @param BindingInterface|CallableAggregateTrait  $context
     */
    public function __construct(AbstractContainer $container, BindingInterface $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    protected function normalizeInvokeArgs(array &$args)
    {
        if (($clone = reset($args)) && ! isset($args[1])) {
            $args[] = $clone;
        }
    }

    public function commit() : BindingInterface
    {
        $this->committedAggregate = array_merge($this->committedAggregate, $this->aggregate);
        $this->aggregate = [];

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
