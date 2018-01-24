<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\Binding\{
    AggregateBindingInterface, BindingInterface
};

interface AggregateBindingProxyInterface extends BindingInterface
{
    public function resolve() : AggregateBindingInterface;
}
