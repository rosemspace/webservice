<?php

namespace TrueCode\Container;

class ReflectionContainer extends Container
{
    /**
     * Make with binding.
     *
     * @param string  $abstract
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(string $abstract, array ...$args)
    {
        if (! $this->has($abstract)) {
            return $this->bindForce($abstract)->make(...$args);
        }

        return $this->bindings[$abstract]->make(...$args);
    }
}
