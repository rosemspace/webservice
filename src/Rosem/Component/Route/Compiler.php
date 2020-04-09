<?php

namespace Rosem\Component\Route;

class Compiler implements CompilerInterface
{
    /**
     * @var ParserInterface
     */
    protected ParserInterface $parser;

    public function __construct(ParserInterface $parser)
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
