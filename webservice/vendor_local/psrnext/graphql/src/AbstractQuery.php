<?php

namespace Psrnext\GraphQL;

abstract class AbstractQuery extends AbstractNode implements QueryInterface
{
    /**
     * @var array
     */
    protected $arguments = [];

    public function addArguments(array $arguments): void
    {
        /** @noinspection AdditionOperationOnArraysInspection */
        $this->arguments += $arguments;
    }
}
