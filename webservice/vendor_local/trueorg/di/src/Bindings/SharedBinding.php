<?php

namespace True\DI\Bindings;

use TrueStandards\DI\AbstractBinding;

class SharedBinding extends AbstractBinding
{
    public function make(array &...$args)
    {
        return $this->concrete;
    }

    public function isShared() : bool
    {
        return true;
    }
}
