<?php

namespace Rosem\Container\Definition;

use Rosem\Container\AbstractContainer;

abstract class AbstractDefinition implements DefinitionInterface
{
    use Aggregate\FactoryTrait;

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

    public function commit() : DefinitionInterface
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
