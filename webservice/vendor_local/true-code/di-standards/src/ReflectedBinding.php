<?php

namespace TrueStandards\DI;

use ReflectionClass;
use SplFixedArray;

abstract class ReflectedBinding extends AbstractBinding
{
    /**
     * @var ContainerInterface
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

    public function __construct(ContainerInterface $container, $concrete, array $args = [])
    {
        parent::__construct($concrete);

        $this->container = $container;
        $this->args = $args;
    }

    protected abstract function reflect();

    /**
     * Get stack of classes and parameters for automatic building
     *
     * @return SplFixedArray $stack
     */
    protected function getStack() : SplFixedArray
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function build(array &$args)
    {
        $stack = $this->getStack();
        $stackLength = count($stack);
        $building = [];

        while ($stackLength) {
            $item = $stack[--$stackLength];

            if ($item instanceof ReflectionClass) {
                if ($this->container->has($item->name) && $this->container->isShared($item->name))
                {
                    $building[] = $this->container->make($item->name, $args);
                } else {
                    $this->container->bind($item->name, $item->name);
                    $building[] = $this->container->make($item->name, array_shift($args) ?: []);
                }
            } else if ($args) {
                $building[] = array_shift($args);
            }
        }

        return $building;
    }
}
