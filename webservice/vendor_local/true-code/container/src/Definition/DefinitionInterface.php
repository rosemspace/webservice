<?php

namespace TrueCode\Container\Definition;

interface DefinitionInterface
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

    public function withMethodCall(string $method, array $args = []) : Aggregate\AggregatedDefinitionInterface;

    public function withFunctionCall(callable $function, array $args = []) : Aggregate\AggregatedDefinitionInterface;

    public function getAbstract() : string;

    public function getConcrete();
}
