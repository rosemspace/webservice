<?php

namespace True\DI\Binding;

use ReflectionClass;
use SplFixedArray;
use True\DI\AbstractReflectedBinding;

class ClassBinding extends AbstractReflectedBinding
{
    /**
     * @var ReflectionClass
     */
    protected $reflector;

    protected function reflect() : void
    {
        if (! $this->reflector) {
            $this->reflector = new ReflectionClass($this->concrete);
            $constructor = $this->reflector->getConstructor();

            if ($constructor) {
                $this->params = SplFixedArray::fromArray($constructor->getParameters());
            }
        }
    }

    /**
     * @param array[] ...$args
     *
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        $this->reflect();

        return $this->params
            ? $this->reflector->newInstanceArgs($this->build(...$args ?: $this->args))
            : new $this->concrete;
    }
}
