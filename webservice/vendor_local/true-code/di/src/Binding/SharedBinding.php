<?php

namespace True\DI\Binding;

use True\DI\AbstractBinding;

class SharedBinding extends AbstractBinding
{
    public function make(array &...$args)
    {
        return $this->concrete;
    }
}
