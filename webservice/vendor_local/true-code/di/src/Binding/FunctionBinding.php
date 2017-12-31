<?php

namespace True\DI\Binding;

use ReflectionFunction;
use SplFixedArray;
use True\DI\AbstractReflectedBinding;

class FunctionBinding extends AbstractReflectedBinding
{
    /**
     * @var ReflectionFunction
     */
    protected $reflector;

    protected function reflect() : void
    {
        if (! $this->reflector) {
            $this->reflector = new ReflectionFunction($this->concrete);
            $this->params = SplFixedArray::fromArray($this->reflector->getParameters());
        }
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        $this->reflect();

        return $this->params
            ? $this->reflector->invokeArgs($this->build(...$args ?: $this->args))
            : call_user_func($this->concrete);
    }
}
