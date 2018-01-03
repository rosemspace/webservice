<?php

namespace True\DI;

abstract class AbstractAggregateBinding implements BindingInterface
{
    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var BindingInterface
     */
    protected $context;

    /**
     * @var array
     */
    protected $aggregate;

    public function __construct(AbstractContainer $container, BindingInterface $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    public function getConcrete() : string
    {
        return $this->context->getConcrete();
    }
}
