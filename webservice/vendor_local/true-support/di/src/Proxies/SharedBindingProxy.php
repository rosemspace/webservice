<?php

namespace True\Support\DI\Proxies;

use True\Support\DI\Bindings\SharedBinding;

class SharedBindingProxy extends BindingProxy
{
    /**
     * @var array
     */
    protected $args;

    public function __construct(&$bindings, $abstract, $concrete, $args)
    {
        parent::__construct($bindings, $abstract, $concrete);

        $this->args = $args;
    }

    public function make(array &...$args)
    {
        return parent::make(...$this->args ?: $this->args = $args);
    }

    protected function getBinding($concrete)
    {
        return new SharedBinding(parent::getBinding($this->concrete)->make(...$this->args));
    }

    public function isShared() : bool
    {
        return true;
    }
}
