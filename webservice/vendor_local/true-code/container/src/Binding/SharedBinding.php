<?php

namespace TrueCode\Container\Binding;

class SharedBinding extends AbstractBinding implements SharedBindingInterface
{
    public function make(array &...$args)
    {
        return $this->concrete;
    }
}
