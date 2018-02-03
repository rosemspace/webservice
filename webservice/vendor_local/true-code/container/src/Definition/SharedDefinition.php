<?php

namespace TrueCode\Container\Definition;

class SharedDefinition extends AbstractDefinition implements SharedDefinitionInterface
{
    public function make(array &...$args)
    {
        return $this->concrete;
    }
}
