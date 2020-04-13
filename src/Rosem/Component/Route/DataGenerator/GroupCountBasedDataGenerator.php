<?php

namespace Rosem\Component\Route\DataGenerator;

use Rosem\Component\Route\Contract\RegexRouteInterface;

use function count;
use function str_repeat;

class GroupCountBasedDataGenerator extends AbstractRegexBasedDataGenerator
{
    public const KEY_REGEX = 0;

    public const KEY_OFFSET = 1;

    protected int $chunkCount = 0;

    protected int $groupCount = 0;

    /**
     * GroupCountBasedChunk constructor.
     *
     * @param int      $routeCountPerRegex
     * @param int|null $regexMaxLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        int $routeCountPerRegex = 10,
        ?int $regexMaxLength = null
    ) {
        parent::__construct($routeCountPerRegex, $regexMaxLength);
    }

    public function newChunk(): void
    {
        $this->regexTree->clear();
        $this->groupCount = 0;
        ++$this->chunkCount;
        $this->lastInsertId += $this->routeCountPerRegex; // TODO: check if no error
        $this->routeExpressions[] = [
            self::KEY_REGEX => '',
            self::KEY_OFFSET => $this->lastInsertId,
        ];
    }

    /**
     * @param RegexRouteInterface $route
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addRoute(RegexRouteInterface $route): void
    {
        $this->lastInsertId = $this->routeCountPerRegex * $this->chunkCount;

        if (!$this->chunkCount || count($this->routeData) - $this->lastInsertId >= $this->routeCountPerRegex) {
            $this->newChunk();
        }

        $variableCount = count($route->getVariableNames());
        $this->groupCount = max($this->groupCount, $variableCount);
        // TODO: check if route regex has groups
        $this->addRegex($route->getRegex() . str_repeat('()', $this->groupCount - $variableCount));
        $this->routeExpressions[count($this->routeExpressions) - 1][self::KEY_REGEX] =
            '~^' . $this->regex . '$~sD' . ($this->utf8 ? 'u' : '');
        ++$this->groupCount; // +1 for first regex matching / next route index
        $middleware = &$route->getMiddlewareExtensions();
        $this->routeData[$this->lastInsertId + $this->groupCount] =
            [$route->getMethods(), $route->getHandler(), &$middleware, $route->getVariableNames()];
    }
}
