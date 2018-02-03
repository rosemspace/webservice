<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\Binding\{
    AggregateBindingInterface, BindingInterface, AbstractBinding
};

class BindingProxy extends AbstractBinding implements BindingProxyInterface
{
    /**
     * @var bool
     */
    protected $committed = false;

    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregateBindingInterface
     */
    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        return (new AggregateBindingProxy($this->container, $this))->withMethodCall($method, $args);
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregateBindingInterface
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        return (new AggregateBindingProxy($this->container, $this))->withFunctionCall($function, $args);
    }

    public function resolve() : BindingInterface
    {
        return $this->container->forceBind($this->getAbstract(), $this->getConcrete(), ...$this->args);
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
        return $this->committed
            ? $this->resolve()->make(...$args)
            : $this->resolve()->commit()->make(...$args);
    }

    public function commit() : BindingInterface
    {
        $this->committed = true;

        return parent::commit();
    }
}
