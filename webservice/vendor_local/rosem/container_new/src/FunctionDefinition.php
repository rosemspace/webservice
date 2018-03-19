<?php

namespace Rosem\Container;

use ReflectionFunction;

class FunctionDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionFunction
     */
    protected $reflector;

    /**
     * FunctionDefinition constructor.
     *
     * @param string|\Closure $concrete
     * @param array           $arguments
     *
     * @throws \ReflectionException
     */
    public function __construct($concrete, array $arguments = [])
    {
        parent::__construct($concrete, $arguments);

        $this->reflector = new ReflectionFunction($concrete);

        if ($parameters = $this->reflector->getParameters()) {
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
        return $this->reflector->invokeArgs($args);
    }
}
