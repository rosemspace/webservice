<?php

namespace Rosem\Component\Route\DataGenerator;

use Rosem\Component\Route\RegexRouteInterface;
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
     * @param RegexRouteInterface $route
     *
     * @return void
     * @throws \Rosem\Component\Route\Exception\TooLongRouteException
     */
    public function addRoute(RegexRouteInterface $route): void
    {
        $this->lastInsertId = count($this->routeData);

        if ($this->lastInsertId - $this->lastChunkOffset >= $this->routeCountPerRegex) {
            $this->newChunk();
        }

        $this->addRegex($route->getRegex() . '(*:' . $this->lastInsertId . ')');
        $this->routeExpressions[count($this->routeExpressions) - 1] =
            '~^' . $this->regex . '$~sD' . ($this->utf8 ? 'u' : '');
        $middleware = &$route->getMiddlewareExtensions();
        $this->routeData[] = [$route->getHandler(), &$middleware, $route->getVariableNames()];
    }
}
