<?php

namespace True\Standards\DI\Bindings;

abstract class AbstractBinding implements BindingInterface
{
    /**
     * @var string
     */
    protected $concrete;

    public function __construct($concrete)
    {
        $this->concrete = $concrete;
    }

    public function isShared() : bool
    {
        return false;
    }
}
