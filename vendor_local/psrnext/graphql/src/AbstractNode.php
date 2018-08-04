<?php

namespace Psrnext\GraphQL;

abstract class AbstractNode implements DescriptionInterface
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
