<?php

namespace True\DI;

use ReflectionClass;
use SplFixedArray;

abstract class AbstractReflectedBinding extends AbstractBinding
{
    /**
     * @var AbstractContainer
     */
    protected $container;

    /**
     * @var \Reflector
     */
    protected $reflector;

    /**
     * @var \ReflectionParameter[]
     */
    protected $params;

    /**
     * @var \SplFixedArray
     */
    protected $stack;

    /**
     * @var array
     */
    protected $args;

    public function __construct(AbstractContainer $container, $concrete, array $args = [])
    {
        parent::__construct($concrete);

        $this->container = $container;
        $this->args = $args;
    }

    protected abstract function reflect(): void;

    /**
     * Get stack of classes and parameters for automatic building
     *
     * @return SplFixedArray $stack
     */
    protected function getStack(): SplFixedArray
    {

        if (! $this->stack) {
            $params = &$this->params;
            $index = -1;
            $length = count($params);
            $stack = new SplFixedArray($length);

            while ($length) {
                $stack[++$index] = $params[--$length]->getClass() ?: $params[$length];
            }

            $this->stack = $stack;
        }

        return $this->stack;
    }

    /**
     * Build and inject all dependencies with parameters
     *
     * @param array $args
     *
     * @return array $building
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function build(?array &$args = null) : array
    {
        if (! $args) { // TODO: improve | improve array_shift below
            $args = [];
        }

        $stack = $this->getStack();
        $stackLength = count($stack);
        $building = [];

        while ($stackLength) {
            $item = $stack[--$stackLength];

            if ($item instanceof ReflectionClass) {
                if ($this->container->has($item->name) && $this->container->isShared($item->name)) {
                    $building[] = $this->container->make($item->name, $args);
                } else {
                    $building[] = $this->container->make($item->name, reset($args) ?: []);
                    unset($args[0]);
                }
            } else if ($args) {
                $building[] = reset($args);
                unset($args[0]);
            }
        }

        return $building;
    }
}
