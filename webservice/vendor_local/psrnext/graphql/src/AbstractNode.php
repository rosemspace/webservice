<?php

namespace Psrnext\GraphQL;

abstract class AbstractNode implements DescribableInterface
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
