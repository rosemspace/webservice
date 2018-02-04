<?php

namespace Rosem\Container\Definition\Aggregate;

trait ProcessTrait
{
    /**
     * @param array[] ...$args
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    abstract protected function process(array &...$args) : array;

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function invoke(array &...$args)
    {
        return $this->process(...$args)[0];
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
        return $this->process(...$args)[1];
    }
}
