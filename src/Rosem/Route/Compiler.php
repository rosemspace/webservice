<?php

namespace Rosem\Route;

class Compiler implements CompilerInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function compile(array $methods, string $routePattern, string $handler): RegexRouteInterface
    {
        return new Route($methods, $handler, $routePattern, ...$this->parser->parse($routePattern)[0]);
    }
}
