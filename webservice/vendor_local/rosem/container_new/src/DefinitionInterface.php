<?php

namespace Rosem\Container;

interface DefinitionInterface
{
    /**
     * @param array[] $args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array $args);

    public function getArguments(): array;

    public function getParameters() : array;

//    public function commit() : self;

//    public function withMethodCall(string $method, array $args = []) : AggregatedDefinitionInterface;

//    public function withFunctionCall(callable $function, array $args = []) : AggregatedDefinitionInterface;

//    public function getConcrete();
}
