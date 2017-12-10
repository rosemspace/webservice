<?php

namespace True\Standards\DI\Bindings;

interface BindingInterface
{
    public function make(array &...$args);

    public function isShared() : bool;
}
