<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\{
    AbstractContainer,
    ExtractorTrait
};

abstract class AbstractAggregateBinding implements AggregateBindingInterface
{
    use ExtractorTrait;

    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var BindingInterface
     */
    protected $context;

    /**
     * @var BindingInterface[]
     */
    protected $aggregate = [];

    /**
     * @var BindingInterface[]
     */
    protected $aggregateCommitted = [];

    /**
     * AbstractAggregateBinding constructor.
     *
     * @param AbstractContainer $container
     * @param BindingInterface  $context
     */
    public function __construct(AbstractContainer $container, BindingInterface $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    protected function normalizeArgs(array &$args)
    {
        if (($clone = reset($args)) && ! isset($args[1])) {
            $args[] = $clone;
        }
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
        return $this->context->make($this->extractFirst($args));
    }

    public function commit() : BindingInterface
    {
        $this->aggregateCommitted = array_merge($this->aggregateCommitted, $this->aggregate);
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
