<?php

namespace Rosem\Container;

class Definition
{
    protected $definition;

    public function __construct($definition)
    {
        $this->definition = $definition;
    }

    public function get()
    {
        return $this->definition;
    }
}
