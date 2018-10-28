<?php

namespace Rosem\Container;

use Psr\Container\ContainerInterface;

class Definition
{
    /**
     * @var \Closure
     */
    private $initializingFactory;

    /**
     * @var \Closure[]
     */
    private $extendingFactories = [];

    public function __construct(ContainerInterface $container, $factory)
    {
        $this->initializingFactory = function () use (&$container, &$factory) {
            if (\is_array($factory) && \is_string(reset($factory))) {
                $factory[key($factory)] = $container->get(reset($factory));
            }

            $result = $factory($container);

            foreach ($this->extendingFactories as $factory) {
                $factory($container, $result);
            }

            return $result;
        };
    }

    public function create()
    {
        return \call_user_func($this->initializingFactory);
    }

    public function extend($factory): void
    {
        $this->extendingFactories[] = $factory;
    }
}
