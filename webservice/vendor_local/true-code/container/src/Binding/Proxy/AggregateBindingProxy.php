<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\Binding\{
    AbstractAggregateBinding, AggregateBindingInterface, BindingInterface
};
use TrueCode\Container\ExtractorTrait;

class AggregateBindingProxy extends AbstractAggregateBinding implements AggregateBindingProxyInterface
{
    use ExtractorTrait;

    protected const METHOD = 0;

    protected const FUNCTION = 1;

    /**
     * @var BindingProxyInterface
     */
    protected $context;

    protected $aggregate = [[], []];

    protected $committedAggregate = [[], []];

    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[self::METHOD][$method] = $args;

        return $this;
    }

    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[self::FUNCTION][] = [$function, $args];

        return $this;
    }

    public function resolve() : AggregateBindingInterface
    {
        $contextBinding = $this->context->resolve();

        if ($this->committedAggregate[self::METHOD]) {
            foreach ($this->committedAggregate[self::METHOD] as $method => $args) {
                $contextBinding = $contextBinding->withMethodCall($method, $args);
            }

            $contextBinding->commit();
        }

        if ($this->committedAggregate[self::FUNCTION]) {
            foreach ($this->committedAggregate[self::FUNCTION] as [$function, $args]) {
                $contextBinding = $contextBinding->withFunctionCall($function, $args);
            }

            $contextBinding->commit();
        }

        if ($this->aggregate[self::METHOD]) {
            foreach ($this->aggregate[self::METHOD] as $method => $args) {
                $contextBinding = $contextBinding->withMethodCall($method, $args);
            }
        }

        if ($this->aggregate[self::FUNCTION]) {
            foreach ($this->aggregate[self::FUNCTION] as [$function, $args]) {
                $contextBinding = $contextBinding->withFunctionCall($function, $args);
            }
        }

        return $contextBinding;
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
        return $this->resolve()->make(...$args);
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function call(array &...$args)
    {
        return $this->resolve()->call(...$args);
    }

    public function commit() : BindingInterface
    {
        $this->committedAggregate = [
            self::METHOD   => array_merge($this->committedAggregate[self::METHOD], $this->aggregate[self::METHOD]),
            self::FUNCTION => array_merge($this->committedAggregate[self::FUNCTION], $this->aggregate[self::FUNCTION]),
        ];
        $this->aggregate = [[], []];

        return $this->container->set($this->getAbstract(), $this);
    }
}
