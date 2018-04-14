<?php

namespace Rosem\Container;

use Psr\Container\ContainerInterface;

class DefinitionProxy
{
    /**
     * @var \Closure
     */
    private $initializingFactory;

    /**
     * @var \Closure[]
     */
    private $extendingFactories = [];

    public function __construct(ContainerInterface $container, &$placeholder, $factory)
    {
        $this->initializingFactory = function () use (&$container, &$placeholder, &$factory) {
            if (\is_array($factory) && \is_string(reset($factory))) {
                $factory[key($factory)] = $container->get(reset($factory));
            }

            $result = \call_user_func($factory, $container);

            foreach ($this->extendingFactories as $factory) {
                \call_user_func($factory, $container, $result);
            }

            $placeholder = new Definition($result);

            return $result;
        };
    }

    public function get()
    {
        return \call_user_func($this->initializingFactory);
    }

    public function extend($factory): void
    {
        $this->extendingFactories[] = $factory;
    }
}
