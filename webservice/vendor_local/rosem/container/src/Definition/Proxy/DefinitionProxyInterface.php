<?php

namespace Rosem\Container\Definition\Proxy;

use Rosem\Container\Definition\DefinitionInterface;

interface DefinitionProxyInterface extends DefinitionInterface
{
    public function resolve() : DefinitionInterface;
}
