<?php

namespace True\Support\DI\Bindings;

use ReflectionFunction;
use SplFixedArray;
use True\Standards\DI\Bindings\ReflectedBinding;

class CallableBinding extends ReflectedBinding
{
    /**
     * @var ReflectionFunction
     */
    protected $reflector;

    public function make(array &...$args)
    {
        $this->reflect();

        return $this->params
            ? $this->reflector->invokeArgs($this->build($args[0]))
            : call_user_func($this->concrete);
    }

    protected function reflect()
    {
        if (! $this->reflector) {
            $this->reflector = new ReflectionFunction($this->concrete);
            $this->params = SplFixedArray::fromArray($this->reflector->getParameters());
        }
    }
}
