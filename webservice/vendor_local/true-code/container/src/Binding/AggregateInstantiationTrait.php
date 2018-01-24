<?php

namespace TrueCode\Container\Binding;

trait AggregateInstantiationTrait
{
    /**
     * @param array[] ...$args
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    abstract protected function invoke(array &...$args) : array;

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        return $this->invoke(...$args)[0];
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function call(array &...$args)
    {
        return $this->invoke(...$args)[1];
    }
}
