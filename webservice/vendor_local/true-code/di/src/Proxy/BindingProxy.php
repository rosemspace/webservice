<?php

namespace True\DI\Proxy;

use True\DI\Binding\{BindingInterface, AbstractBinding};

class BindingProxy extends AbstractBinding
{
    /**
     * @var array
     */
    protected $methodAggregate = [];

    /**
     * @var array
     */
    protected $functionAggregate = [];

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        $this->methodAggregate[$method] = $args;

        return $this;
    }

    public function withFunctionCall(callable $function, array $args = []) : BindingInterface
    {
        $this->functionAggregate[] = [$function, $args];

        return $this;
    }

    public function getContext(array &$args) : BindingInterface
    {
        $resolvedArgs = array_map(function ($args, $defaultArgs) {
            return $args ?: $defaultArgs ?: [];
        }, $args, $this->args);

        return $this->container->bindForce($this->abstract, $this->concrete, ...$resolvedArgs);
    }

    public function getContextWithCalls(array &$args) : BindingInterface
    {
        $binding = $this->getContext($args);

        if ($this->methodAggregate) {
            foreach ($this->methodAggregate as $method => $args) {
                $binding = $binding->withMethodCall($method, $args);
            }
        }

        if ($this->functionAggregate) {
            foreach ($this->functionAggregate as [$function, $args]) {
                $binding = $binding->withFunctionCall($function, $args);
            }
        }

        return $binding;
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
        return $this->getContextWithCalls($args)->commit()->make();
    }
}
