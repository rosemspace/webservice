<?php

namespace True\Support\DI\Bindings;

use True\Standards\DI\Bindings\AbstractBinding;

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
