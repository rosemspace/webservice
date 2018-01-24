<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\AbstractContainer;

abstract class AbstractAggregateBinding implements AggregateBindingInterface
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

    abstract protected function invoke(array &...$args);

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        return $this->invoke(...$args)[0];
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function call(array &...$args)
    {
        return $this->invoke(...$args)[1];
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
