<?php

namespace Rosem\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    /**
     * @var ContainerInterface
     */
    private $delegate;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    /**
     * @param string $className
     * @param array  $args
     *
     * @return DefinitionInterface
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function class(string $className, array $args = []) : DefinitionInterface
    {
        if (class_exists($className)) {
            return new ClassDefinition($className, $args);
        }

        throw new \InvalidArgumentException('Invalid class name');
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return FunctionDefinition
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function function (callable $function, array $args = []) : FunctionDefinition
    {
        if (\is_string($function) || $function instanceof \Closure) {
            return new FunctionDefinition($function, $args);
        }

        throw new \InvalidArgumentException('Not a function');
    }

    protected function set(string $id, DefinitionInterface $definition) : void
    {
        $this->definitions[$id] = $definition;
    }

    private function &extractFirst(array &$args)
    {
        if (false === $value = reset($args)) {
            $value = [];
        }

        unset($args[key($args)]);

        return $value;
    }

    /**
     * Build and inject all dependencies with parameters
     *
     * @param iterable $parameters
     * @param iterable $args
     *
     * @return array $building
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function build(iterable $parameters, iterable $args = []) : array
    {
        $stackLength = \count($parameters);
        $building = [];

        while ($stackLength) {
            $parameter = $parameters[--$stackLength];

            if ($parameter instanceof ReflectionClass && !\is_object(reset($args))) {
                $building[] = $this->make(
                    $parameter->name,
                    $this->has($parameter->name) && $this->isShared($parameter->name)
                        ? $args
                        : $this->extractFirst($args)
                );
            } elseif ($args) {
                $building[] = $this->extractFirst($args);
            }
        }

        return $building;
    }

    /**
     * @param         $abstract
     * @param array[] ...$args
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function make($abstract, array ...$args)
    {
        $definition = &$this->definitions[$abstract];

        return $definition->make(
            $this->build(
                $definition->getParameters(),
                reset($args) ?: $definition->getArguments()
            )
        );
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get($id)
    {
        return $this->make($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id) : bool
    {
        if (isset($this->definitions[$id])) {
            return true;
        }

        if ($this->delegate) {
            return $this->delegate->has($id);
        }

        return false;
    }

    private function isShared(string $abstract) : bool
    {

    }
}
