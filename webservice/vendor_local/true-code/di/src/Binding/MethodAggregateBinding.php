<?php

namespace True\DI\Binding;

use True\DI\AbstractAggregateBinding;
use True\DI\AbstractContainer;
use True\DI\BindingInterface;
use True\DI\ReflectedBuildTrait;

class MethodAggregateBinding extends AbstractAggregateBinding
{
    use ReflectedBuildTrait;

    public function __construct(AbstractContainer $container, BindingInterface $context, array $aggregate = [])
    {
        parent::__construct($container, $context);

        foreach ($aggregate as $method => $args) {
            $this->withMethodCall($method, $args);
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
        $context = $this->context->make($this->extractFirst($args));
        reset($args);

        foreach ($this->aggregate as $method) {
            $method->make($context, current($args) ?: []);
            next($args);
        }

        return $context;
    }

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        $this->aggregate[$method] = new MethodBinding($this->container, $this->context->getConcrete(), $method, $args);

        return $this;
    }
}
