<?php

namespace Psrnext\GraphQL;

abstract class AbstractNode implements NodeInterface
{
    public function getDescription(): string
    {
        return '';
    }
}
