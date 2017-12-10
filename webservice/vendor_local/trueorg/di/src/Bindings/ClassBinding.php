<?php

namespace True\DI\Bindings;

use ReflectionClass;
use SplFixedArray;
use TrueStandards\DI\ReflectedBinding;

class ClassBinding extends ReflectedBinding
{
    /**
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * @param array[] ...$args
     *
     * @return object
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     * @throws \TrueStandards\DI\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        $this->reflect();
        return $this->params
            ? $this->reflector->newInstanceArgs($this->build($args[0]))
            : new $this->concrete;
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
