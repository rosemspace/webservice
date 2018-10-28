<?php

namespace Rosem\Route;

use Rosem\Psr\Route\RouteDispatcherInterface;
use Rosem\Route\DataGenerator\MarkBasedDataGenerator;
use Rosem\Route\DataGenerator\StringNumberBasedDataGenerator;
use Rosem\Route\DataGenerator\GroupCountBasedDataGenerator;
use Rosem\Route\Dispatcher\MarkBasedDispatcher;
use Rosem\Route\Dispatcher\StringNumberBasedDispatcher;
use Rosem\Route\Dispatcher\GroupCountBasedDispatcher;

class Router extends Collector implements RouteDispatcherInterface
{
    use RegexBasedDispatcherTrait;

    /**
     * Router constructor.
     *
     * @param int $routeCountPerRegex
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $routeCountPerRegex = PHP_INT_MAX)
    {
        parent::__construct(new Compiler(new Parser()), new MarkBasedDataGenerator($routeCountPerRegex));
//        parent::__construct(new Compiler(new Parser()), new StringNumberBasedDataGenerator());
//        parent::__construct(new Compiler(new Parser()), new GroupCountBasedDataGenerator());

//        $this->regexBasedDispatcher = new MarkBasedDispatcher();
        $this->regexBasedDispatcher = new StringNumberBasedDispatcher();
//        $this->regexBasedDispatcher = new GroupCountBasedDispatcher();
    }

    public function dispatch(string $method, string $uri): array
    {
        if (isset($this->staticRouteMap[$method][$uri])) {
            [$handler, $middleware] = $this->staticRouteMap[$method][$uri];

            return [200, &$handler, &$middleware, []];
        }

        return $this->regexBasedDispatcher->dispatch(
            $this->variableRouteMap[$method]->routeExpressions,
            $this->variableRouteMap[$method]->routeData,
            $uri
        );
    }
}
