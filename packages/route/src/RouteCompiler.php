<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use Rosem\Component\Route\Contract\{
    RouteCompilerInterface,
    RouteParserInterface
};

class RouteCompiler implements RouteCompilerInterface
{
    protected RouteParserInterface $parser;

    public function __construct(RouteParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function compile(array $methods, string $routePattern, string $handler): array
    {
        $routes = [];

        foreach ($this->parser->parse($routePattern) as $routeData) {
            $routes[] = new Route($methods, $handler, $routePattern, ...$routeData);
        }

        return $routes;
    }
}
