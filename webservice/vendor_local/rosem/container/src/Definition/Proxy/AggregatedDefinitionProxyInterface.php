<?php

namespace Rosem\Container\Definition\Proxy;

use Rosem\Container\Definition\Aggregate\AggregatedDefinitionInterface;

interface AggregatedDefinitionProxyInterface extends AggregatedDefinitionInterface
{
    public function resolve() : AggregatedDefinitionInterface;
}
