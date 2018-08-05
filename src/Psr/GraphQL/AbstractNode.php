<?php

namespace Rosem\Psr\GraphQL;

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
