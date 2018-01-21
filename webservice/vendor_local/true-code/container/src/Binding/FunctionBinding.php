<?php

namespace TrueCode\Container\Binding;

use ReflectionFunction;
use SplFixedArray;
use TrueCode\Container\AbstractContainer;

class FunctionBinding extends AbstractBinding
{
    use ReflectedBuildTrait;

    /**
     * FunctionBinding constructor.
     *
     * @param AbstractContainer $container
     * @param string            $abstract
     * @param callable          $concrete
     * @param array             $args
     *
     * @throws \ReflectionException
     */
    public function __construct(AbstractContainer $container, string $abstract, callable $concrete, array $args = [])
    {
        parent::__construct($container, $abstract, $concrete, $args);

        $this->reflector = new ReflectionFunction($this->concrete);

        if ($params = SplFixedArray::fromArray($this->reflector->getParameters())) {
            $this->stack = $this->getStack($params);
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
        return $this->reflector->invokeArgs($this->build($this->stack, reset($args) ?: $this->args));
    }
}
