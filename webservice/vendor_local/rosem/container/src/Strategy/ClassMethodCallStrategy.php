<?php

namespace Rosem\Container\Strategy;

use Rosem\Container\AbstractContainer;
use Rosem\Container\Exception\NotFoundException;

class DoubleStringCallStrategy
{
    protected $container;
    protected $containerDelegate;

    public function __construct(
        AbstractContainer $container,
        ?AbstractContainer $containerDelegate = null
    ) {
        $this->container = $container;
        $this->containerDelegate = $containerDelegate;
    }

    /**
     * @param string[] $abstract
     * @param array    $args
     * @param bool     $proceed
     *
     * @return mixed
     * @throws NotFoundException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function process(array $abstract, array $args, bool &$proceed)
    {
        if ($definition = $this->container->find(reset($abstract))) {
            return $definition->withMethodCall(next($abstract))->call(...$args);
        }

        if ($this->containerDelegate) {
            return $this->containerDelegate->call($abstract, ...$args);
        }

        $proceed = true;

        return null;
    }

    public function make($abstract, array ...$args)
    {
        $proceed = false;
        $result = $this->process($abstract, $args, $proceed);

        if ($proceed) {
            throw new NotFoundException('Definition not found');
        }

        return $result;
    }
}
