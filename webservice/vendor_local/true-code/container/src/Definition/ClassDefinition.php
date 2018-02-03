<?php

namespace TrueCode\Container\Definition;

use ReflectionClass;
use SplFixedArray;
use TrueCode\Container\AbstractContainer;

class ClassDefinition extends AbstractDefinition
{
    use ReflectedBuildTrait;

    /**
     * ClassBinding constructor.
     *
     * @param AbstractContainer $container
     * @param string            $abstract
     * @param string            $concrete
     * @param array             $args
     *
     * @throws \ReflectionException
     */
    public function __construct(AbstractContainer $container, string $abstract, $concrete = null, array $args = [])
    {
        parent::__construct($container, $abstract, $concrete, $args);

        $this->reflector = new ReflectionClass($this->getConcrete());

        if (
            ($constructor = $this->reflector->getConstructor()) &&
            ($params = $constructor->getParameters())
        ) {
            $this->stack = $this->getStack(SplFixedArray::fromArray($params));
        }
    }

    /**
     * @param array[] ...$args
     *
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        return $this->reflector->newInstanceArgs($this->build($this->stack, reset($args) ?: $this->args));
    }
}
