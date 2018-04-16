<?php

namespace Psrnext\GraphQL;

abstract class AbstractNode implements NodeInterface
{
    /**
     * @var \Closure[]
     */
    protected $factories = [];

    public function getDescription(): string
    {
        return '';
    }
}
