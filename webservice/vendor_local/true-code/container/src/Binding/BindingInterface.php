<?php

namespace TrueCode\Container\Binding;

interface BindingInterface
{
    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args);

    public function commit() : self;

    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface;

    public function withFunctionCall(callable $function, array $args = []) : AggregateBindingInterface;

    public function getAbstract() : string;

    public function getConcrete();
}
