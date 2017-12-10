<?php

namespace True\Standards\DI\Bindings;

use ReflectionClass;
use SplFixedArray;

abstract class ReflectedBinding extends AbstractBinding
{
    use BindingBuilder;

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

    public function __construct(array &$bindings, $concrete)
    {
        parent::__construct($concrete);

        $this->bindings = &$bindings;
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
     */
    protected function build(array &$args)
    {
        $stack = $this->getStack();
        $stackLength = count($stack);
        $building = [];

        while ($stackLength) {
            $item = $stack[--$stackLength];

            if ($item instanceof ReflectionClass) {
                if (isset($this->bindings[$item->name]) && $this->bindings[$item->name]->isShared())
                {
                    $building[] = $this->bindings[$item->name]->make($args);
                } else {
                    $building[] = $this->setAndMake($item->name, array_shift($args) ?: []);
//                    $building[] = $this->container->set($item->name, $this->container->createBinding($item->name));
                }
            } else if ($args) {
                $building[] = array_shift($args);
            }
//            $item instanceof ReflectionClass
//                ? $building[] = (isset($this->bindings[$item->name]) && $this->bindings[$item->name]->isShared())
//                ? $this->bindings[$item->name]->make($args)
//                : $this->setAndMake($item->name, array_shift($args) ?: [])
//                : ! $args ?: $building[] = array_shift($args) ?: [];
        }

        return $building;
    }

    /**
     * Important to use this method because parameters passed through reference.
     *
     * @param string $abstract
     * @param array  $args
     *
     * @return mixed
     */
    protected function setAndMake(string $abstract, array $args)
    {
        return ($this->bindings[$abstract] = $this->getBinding($abstract))->make($args);
    }
}
