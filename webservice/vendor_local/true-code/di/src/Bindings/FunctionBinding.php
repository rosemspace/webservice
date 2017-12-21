<?php

namespace True\DI\Bindings;

use ReflectionFunction;
use SplFixedArray;
use TrueStandards\DI\ReflectedBinding;

class FunctionBinding extends ReflectedBinding
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
     * @throws \TrueStandards\DI\ContainerExceptionInterface
     * @throws \TrueStandards\DI\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        $this->reflect();

        return $this->params
            ? $this->reflector->invokeArgs($this->build(...$args ?: $this->args))
            : call_user_func($this->concrete);
    }
}
