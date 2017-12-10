<?php

namespace True\Standards\DI\Bindings;

use SplFixedArray;

trait BindingBuilder
{
    /**
     * Container all bindings.
     *
     * @var BindingInterface[]
     */
    protected $bindings;

    protected function getBinding($concrete)
    {
        // if $concrete is a string
        if (is_string($concrete)) { // TODO: move colon mark into constant
            if (count($explodedConcrete = explode('::', $concrete, 2)) > 1) {
                return new \True\Support\DI\Bindings\MethodBinding(
                    $this->bindings,
                    SplFixedArray::fromArray($explodedConcrete)
                );
            }

            // if concrete class represent an existed class
            if (class_exists($concrete)) {
                return method_exists($concrete, '__invoke')
                    ? new \True\Support\DI\Bindings\MethodBinding(
                        $this->bindings,
                        SplFixedArray::fromArray([$concrete, '__invoke'])
                    )
                    : new \True\Support\DI\Bindings\ClassBinding($this->bindings, $concrete);
            }
        }

        if (is_array($concrete)) {
            return new \True\Support\DI\Bindings\MethodBinding(
                $this->bindings,
                SplFixedArray::fromArray(
                    count($concrete) > 1 ? $concrete : [array_keys($concrete)[0], array_values($concrete)[0]]
                )
            );
        }

        // if $concrete is callable
        if (is_callable($concrete)) {
            return new \True\Support\DI\Bindings\CallableBinding($this->bindings, $concrete);
        }

        // if $concrete is an instance
        //return $this->getInstanceClosure($placeholder, $concrete);
        return null;
    }
}
