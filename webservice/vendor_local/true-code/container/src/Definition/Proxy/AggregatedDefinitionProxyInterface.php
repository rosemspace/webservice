<?php

namespace TrueCode\Container\Definition\Proxy;

use TrueCode\Container\Definition\Aggregate\AggregatedDefinitionInterface;

interface AggregatedDefinitionProxyInterface extends AggregatedDefinitionInterface
{
    public function resolve() : AggregatedDefinitionInterface;
}
