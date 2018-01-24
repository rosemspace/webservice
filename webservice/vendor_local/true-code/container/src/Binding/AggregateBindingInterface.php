<?php

namespace TrueCode\Container\Binding;

interface AggregateBindingInterface extends BindingInterface
{
    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function call(array &...$args);
}
