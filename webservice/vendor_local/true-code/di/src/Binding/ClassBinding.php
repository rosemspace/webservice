<?php

namespace True\DI\Binding;

use ReflectionClass;
use SplFixedArray;
use True\DI\AbstractBinding;
use True\DI\AbstractContainer;
use True\DI\ReflectedBuildTrait;

class ClassBinding extends AbstractBinding
{
    use ReflectedBuildTrait;

    /**
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * @var SplFixedArray
     */
    protected $stack = [];

    public function __construct(AbstractContainer $container, string $abstract, $concrete, array $args = [])
    {
        parent::__construct($container, $abstract, $concrete, $args);

        $this->reflector = new ReflectionClass($this->concrete);

        if (
            ($constructor = $this->reflector->getConstructor()) &&
            ($params = SplFixedArray::fromArray($constructor->getParameters()))
        ) {
            $this->stack = $this->getStack($params);
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
