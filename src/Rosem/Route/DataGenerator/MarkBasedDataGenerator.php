<?php

namespace Rosem\Route\DataGenerator;

use Rosem\Route\RouteInterface;
use function count;

class MarkBasedDataGenerator extends AbstractRegexBasedDataGenerator
{
    protected $lastChunkOffset = 0;

    /**
     * MarkBasedChunk constructor.
     *
     * @param int      $routeCountPerRegex
     * @param int|null $regexMaxLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        int $routeCountPerRegex = PHP_INT_MAX,
        ?int $regexMaxLength = null
    ) {
        parent::__construct($routeCountPerRegex, $regexMaxLength);

        $this->routeExpressions[] = '';
    }

    public function newChunk(): void
    {
        $this->regexTree->clear();
        $this->lastChunkOffset = $this->lastInsertId;
        $this->routeExpressions[] = '';
    }

    /**
     * @param RouteInterface $route
     *
     * @return void
     * @throws \Rosem\Route\Exception\TooLongRouteException
     */
    public function addRoute(RouteInterface $route): void
    {
        $this->lastInsertId = count($this->routeData);

        if ($this->lastInsertId - $this->lastChunkOffset >= $this->routeCountPerRegex) {
            $this->newChunk();
        }

        $this->addRegex($route->getRegex() . '(*:' . $this->lastInsertId . ')');
        $this->routeExpressions[count($this->routeExpressions) - 1] =
            '~^' . $this->regex . '$~sD' . ($this->utf8 ? 'u' : '');
        $middleware = &$route->getMiddlewareListReference();
        $this->routeData[] = [$route->getHandler(), &$middleware, $route->getVariableNames()];
    }
}
