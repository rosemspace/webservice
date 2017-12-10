<?php

namespace True\Support\DI\Proxies;

use True\Standards\DI\Bindings\{AbstractBinding, BindingBuilder};

class BindingProxy extends AbstractBinding
{
    use BindingBuilder;

    /**
     * @var string
     */
    protected $abstract;

    public function __construct(&$bindings, $abstract, $concrete)
    {
        parent::__construct($concrete);

        $this->bindings = &$bindings;
        $this->abstract = $abstract;
    }

    public function make(array &...$args)
    {
        return ($this->bindings[$this->abstract] = $this->getBinding($this->concrete))->make(...$args);
    }
}
