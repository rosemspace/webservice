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

    public function withMethodCall(string $method, array $args = []) : self;

    public function withFunctionCall(callable $function, array $args = []) : self;

    public function getAbstract() : string;

    public function getConcrete();
}
