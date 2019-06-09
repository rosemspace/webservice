<?php

namespace Rosem\Component\Route;

use Rosem\Contract\Route\RouteDispatcherInterface;

class DispatcherProxy implements RouteDispatcherInterface
{
    private $collector;

    private $regexBasedDispatcher;

    private $placeholder;

    public function __construct(
        Collector $collector,
        RegexBasedDispatcherInterface $regexBasedDispatcher,
        &$placeholder
    ) {
        $this->collector = $collector;
        $this->regexBasedDispatcher = $regexBasedDispatcher;
        $this->placeholder = &$placeholder;
    }

    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * @param string $method
     * @param string $uri
     *
     * @return array The handler and variables
     */
    public function dispatch(string $method, string $uri): array
    {
        $this->placeholder = new Dispatcher(
            $this->collector->getStaticRouteMap(),
            $this->collector->getVariableRouteMap(),
            $this->regexBasedDispatcher
        );

        return $this->placeholder->dispatch($method, $uri);
    }
}
