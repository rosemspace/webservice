<?php

namespace True\Support\DI\Bindings;

use ReflectionClass;
use SplFixedArray;
use True\Standards\DI\Bindings\ReflectedBinding;

class ClassBinding extends ReflectedBinding
{
    /**
     * @var ReflectionClass
     */
    protected $reflector;

    public function make(array &...$args)
    {
        $this->reflect();
        $instance = $this->params
            ? $this->reflector->newInstanceArgs($this->build($args[0]))
            : new $this->concrete;
//        $this->doInjection($instance);

        return $instance;
    }

    protected function reflect()
    {
        if (! $this->reflector) {
            $this->reflector = new ReflectionClass($this->concrete);
            $constructor = $this->reflector->getConstructor();

            if ($constructor) {
                $this->params = SplFixedArray::fromArray($constructor->getParameters());
            }
        }
    }
}
