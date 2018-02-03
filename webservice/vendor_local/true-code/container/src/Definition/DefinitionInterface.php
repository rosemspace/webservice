<?php

namespace TrueCode\Container\Definition;

use TrueCode\Container\Definition\Aggregate\AggregatedDefinitionInterface;

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

    public function withMethodCall(string $method, array $args = []) : AggregatedDefinitionInterface;

    public function withFunctionCall(callable $function, array $args = []) : AggregatedDefinitionInterface;

    public function getAbstract() : string;

    public function getConcrete();
}
