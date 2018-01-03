<?php

namespace True\DI;

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

    public function withMethodCall(string $method, array $args = []) : self;

    public function getConcrete();
}
