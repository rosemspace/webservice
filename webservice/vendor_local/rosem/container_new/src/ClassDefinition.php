<?php

namespace Rosem\Container;

use InvalidArgumentException;
use ReflectionClass;

class ClassDefinition extends AbstractDefinition
{
    /**
     * @var \Reflector
     */
    protected $reflector;

    /**
     * ClassDefinition constructor.
     *
     * @param string $concrete
     * @param array  $arguments
     *
     * @throws \ReflectionException
     */
    public function __construct(string $concrete, array $arguments = [])
    {
        parent::__construct($concrete, $arguments);

        $this->reflector = new ReflectionClass($this->concrete);

        if ($this->reflector->isAbstract()) {
            throw new InvalidArgumentException("Abstract class given {$this->reflector->getName()}");
        }

        if (($constructor = $this->reflector->getConstructor()) &&
            ($parameters = $constructor->getParameters())
        ) {
            $this->parameters = $this->resolveParameters($parameters);
        }
    }

    /**
     * @param array[] $args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array $args)
    {
        return $this->reflector->newInstanceArgs($args);
    }
}
