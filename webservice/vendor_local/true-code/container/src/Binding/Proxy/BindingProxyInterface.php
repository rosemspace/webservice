<?php

namespace TrueCode\Container\Binding\Proxy;

use TrueCode\Container\Binding\BindingInterface;

interface BindingProxyInterface extends BindingInterface
{
    public function resolve() : BindingInterface;
}
