<?php

namespace True\DI\Binding;

use ReflectionFunction;
use SplFixedArray;
use True\DI\AbstractBinding;
use True\DI\ReflectedBuildTrait;

class FunctionBinding extends AbstractBinding
{
    use ReflectedBuildTrait;

    /**
     * @var ReflectionFunction
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
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        if (! $this->reflector) {
            $this->reflector = new ReflectionFunction($this->concrete);

            if ($this->params = SplFixedArray::fromArray($this->reflector->getParameters())) {
                return $this->reflector->invokeArgs(
                    $this->build(
                        $this->stack = $this->getStack($this->params),
                        ! $this->args ? $this->args = reset($args) ?: [] : reset($args) ?: $this->args
                    )
                );
            }

            return call_user_func($this->concrete);
        }

        return $this->params
            ? $this->reflector->invokeArgs(
                $this->build(
                    $this->stack,
                    reset($args) ?: $this->args
                )
            )
            : call_user_func($this->concrete);
    }
}
