<?php

namespace True\DI\Binding;

use ReflectionClass;
use SplFixedArray;
use True\DI\AbstractBinding;
use True\DI\ReflectedBuildTrait;

class ClassBinding extends AbstractBinding
{
    use ReflectedBuildTrait;

    /**
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * @var \ReflectionParameter[]
     */
    protected $params = [];

    /**
     * @var SplFixedArray
     */
    protected $stack = [];

    /**
     * @param array[] ...$args
     *
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        if (! $this->reflector) {
            $this->reflector = new ReflectionClass($this->concrete);

            // TODO: inflection
//            $this->reflector->getInterfaceNames();

            if (
                ($constructor = $this->reflector->getConstructor()) &&
                ($this->params = SplFixedArray::fromArray($constructor->getParameters()))
            ) {
                return $this->reflector->newInstanceArgs(
                    $this->build(
                        $this->stack = $this->getStack($this->params),
                        ! $this->args ? $this->args = reset($args) ?: [] : reset($args) ?: $this->args
                    )
                );
            }

            return new $this->concrete;
        }

        return $this->params
            ? $this->reflector->newInstanceArgs(
                $this->build(
                    $this->stack,
                    reset($args) ?: $this->args
                )
            )
            : new $this->concrete;
    }
}
