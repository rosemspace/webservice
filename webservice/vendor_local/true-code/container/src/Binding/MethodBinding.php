<?php

namespace TrueCode\Container\Binding;

use ReflectionMethod;
use SplFixedArray;
use TrueCode\Container\AbstractContainer;

class MethodBinding
{
    use ReflectedBuildTrait;

    /**
     * @var array
     */
    protected $args;

    /**
     * MethodBinding constructor.
     *
     * @param AbstractContainer $container
     * @param mixed             $context
     * @param string            $method
     * @param array             $args
     *
     * @throws \ReflectionException
     */
    public function __construct(AbstractContainer $container, $context, string $method, array $args = [])
    {
        $this->container = $container;
        $this->args = $args;
        $this->reflector = new ReflectionMethod($context, $method);

        if ($params = SplFixedArray::fromArray($this->reflector->getParameters())) {
            $this->stack = $this->getStack($params);
        }
    }

    public function make($context, array $args = [])
    {
        return $this->reflector->invokeArgs($context, $this->build($this->stack, $args ?: $this->args));
    }
}
