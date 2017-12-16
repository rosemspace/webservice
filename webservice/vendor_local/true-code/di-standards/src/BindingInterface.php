<?php

namespace TrueStandards\DI;

interface BindingInterface
{
    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function make(array &...$args);

    /**
     * @return bool
     */
    public function isShared() : bool;
}
