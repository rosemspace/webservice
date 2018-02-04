<?php

namespace TrueCode\Container\Definition\Proxy;

use TrueCode\Container\Definition\DefinitionInterface;

interface DefinitionProxyInterface extends DefinitionInterface
{
    public function resolve() : DefinitionInterface;
}
