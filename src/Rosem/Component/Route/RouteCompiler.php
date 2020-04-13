<?php

namespace Rosem\Component\Route;

use Rosem\Component\Route\Contract\{
    RouteCompilerInterface,
    RouteParserInterface
};

class RouteCompiler implements RouteCompilerInterface
{
    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $parser;

    public function __construct(RouteParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritDoc
     */
    public function compile(array $methods, string $routePattern, string $handler): array
    {
        $routes = [];

        foreach ($this->parser->parse($routePattern) as $routeData) {
            $routes[] = new Route($methods, $handler, $routePattern, ...$routeData);
        }

        return $routes;
    }
}
