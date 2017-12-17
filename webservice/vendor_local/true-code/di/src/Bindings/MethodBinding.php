<?php

namespace True\DI\Bindings;

use ReflectionMethod;
use SplFixedArray;
use TrueStandards\DI\ReflectedBinding;

class MethodBinding extends ReflectedBinding
{
    /**
     * @var ReflectionMethod
     */
    protected $reflector;

    /**
     * @var object
     */
    protected $context;

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

        if (! $args) {
            $args = $this->args;
        } else {
            if (empty($args[1])) {
                $args = [[], &$args[0]];
            }
        }

        return $this->params
            ? $this->reflector->invokeArgs($this->context->make($args[0]), $this->build($args[1]))
            : call_user_func($this->concrete);
    }

    protected function reflect()
    {
        if (! $this->reflector) {
            $this->context = is_string($this->concrete[0])
                ? new ClassBinding($this->container, $this->concrete[0])
                : new SharedBinding($this->concrete[0]);
            $this->reflector = new ReflectionMethod($this->concrete[0], $this->concrete[1]);
            $this->params = SplFixedArray::fromArray($this->reflector->getParameters());
        }
    }
}
