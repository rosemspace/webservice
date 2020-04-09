<?php

namespace Rosem\Component\Route\DataGenerator;

use Rosem\Component\Route\RegexRouteInterface;

use function count;

class MarkBasedDataGenerator extends AbstractRegexBasedDataGenerator
{
    protected int $lastChunkOffset = 0;

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

        if (!$this->lastInsertId || $this->lastInsertId - $this->lastChunkOffset >= $this->routeCountPerRegex) {
            $this->newChunk();
        }

        $this->addRegex($route->getRegex() . '(*:' . $this->lastInsertId . ')');
        $this->routeExpressions[count($this->routeExpressions) - 1] =
            '~^' . $this->regex . '$~sD' . ($this->utf8 ? 'u' : '');
        $middleware = &$route->getMiddlewareExtensions();
        $this->routeData[] = [$route->getMethods(), $route->getHandler(), &$middleware, $route->getVariableNames()];
    }
}
