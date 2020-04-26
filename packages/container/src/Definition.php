<?php

namespace Rosem\Component\Container;

use Psr\Container\ContainerInterface;
use TypeError;

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
        if (!is_callable($factory)) {
            throw new TypeError('A factory in a service provider should be a callable.');
        }

        if (is_array($factory) && is_string(reset($factory))) {
            [$interface, $method] = $factory;
            $this->initializingFactory = static fn(ContainerInterface $container) =>
            ([$container->get($interface), $method])($container);
        } else {
            $this->initializingFactory = $factory;
        }
    }

    public function create(ContainerInterface $container)
    {
        $result = ($this->initializingFactory)($container);

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
