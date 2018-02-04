<?php

namespace TrueCode\Container\Definition\Aggregate;

use TrueCode\Container\{
    AbstractContainer,
    Definition\DefinitionInterface,
    ExtractorTrait
};

abstract class AbstractAggregatedDefinition implements AggregatedDefinitionInterface
{
    use ExtractorTrait;

    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var DefinitionInterface
     */
    protected $context;

    /**
     * @var DefinitionInterface[]
     */
    protected $aggregate = [];

    /**
     * @var DefinitionInterface[]
     */
    protected $aggregateCommitted = [];

    /**
     * AbstractAggregateBinding constructor.
     *
     * @param AbstractContainer                $container
     * @param DefinitionInterface|FactoryTrait $context
     */
    public function __construct(AbstractContainer $container, DefinitionInterface $context)
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

    public function commit() : DefinitionInterface
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
