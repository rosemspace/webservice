<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\Binding\AggregateBindingInterface;

interface AggregateBindingProxyInterface extends AggregateBindingInterface
{
    public function resolve() : AggregateBindingInterface;
}
