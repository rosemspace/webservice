<?php

namespace True\DI\Binding;

class SharedBinding extends AbstractBinding
{
    public function make(array &...$args)
    {
        return $this->concrete;
    }
}
