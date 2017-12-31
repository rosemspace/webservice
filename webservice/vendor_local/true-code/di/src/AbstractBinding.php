<?php

namespace True\DI;

abstract class AbstractBinding implements BindingInterface
{
    /**
     * @var string
     */
    protected $concrete;

    protected $methodCalls = [];

    public function __construct($concrete)
    {
        $this->concrete = $concrete;
    }

    public function isShared() : bool
    {
        return false;
    }
}
