<?php

namespace TrueCode\Container\Binding;

use ReflectionMethod;
use SplFixedArray;
use TrueCode\Container\AbstractContainer;

class MethodBinding implements DependentBindingInterface
{
    use ReflectedBuildTrait;

    /**
     * @var string
     */
    protected $concrete;

    /**
     * @var array
     */
    protected $args;

    /**
     * MethodBinding constructor.
     *
     * @param AbstractContainer $container
     * @param                   $context
     * @param                   $concrete
     * @param array             $args
     *
     * @throws \ReflectionException
     */
    public function __construct(AbstractContainer $container, $context, $concrete = null, array $args = [])
    {
        $this->container = $container;
        $this->concrete = $concrete;
        $this->args = $args;
        $this->reflector = new ReflectionMethod($context, $this->concrete);

        if ($params = SplFixedArray::fromArray($this->reflector->getParameters())) {
            $this->stack = $this->getStack($params);
        }
    }

    public function make($context, array $args = [])
    {
        return $this->reflector->invokeArgs($context, $this->build($this->stack, $args ?: $this->args));
    }
}
