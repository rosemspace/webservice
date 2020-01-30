<?php

namespace Rosem\Component\Container;

use Closure;
use Psr\Container\ContainerInterface;

class Definition
{
    /**
     * @var callable
     */
    private $initializingFactory;

    /**
     * @var callable[]
     */
    private array $extendingFactories = [];

    /**
     * Definition constructor.
     *
     * @param callable|string[] $factory
     */
    public function __construct($factory)
    {
        if (is_array($factory) && is_string(reset($factory))) {
            [$interface, $method] = $factory;
            $this->initializingFactory = static fn(ContainerInterface $container) =>
                call_user_func([$container->get($interface), $method], $container);
        } else {
            $this->initializingFactory = $factory;
        }
    }

    public function create(ContainerInterface $container)
    {
        $result = call_user_func($this->initializingFactory, $container);

        foreach ($this->extendingFactories as $factory) {
            $factory($container, $result);
        }

        return $result;
    }

    public function extend($factory): void
    {
        $this->extendingFactories[] = $factory;
    }
}
