<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Map;

use Rosem\Component\Route\Contract\RouteInterface;

use function preg_match;

class MarkBasedMap extends AbstractRegexBasedMap
{
    /**
     * @inheritDoc
     */
    protected function createVariableRouteChunk(string $scope): void
    {
        $this->variableRouteRegexTree->clear();
        $this->variableRouteMapExpressions[$scope][] = '';
    }

    /**
     * @inheritDoc
     */
    protected function saveVariableRoute(RouteInterface $route): void
    {
        [$regex, $variableNames] = $route->getMeta();
        // (*:n) - shorthand for (*MARK:n)
        $this->variableRouteRegexTree->addRegex("$regex(*:$this->variableRouteCount)");
        // TODO: data adding strategy / scope functionality?
        $this->variableRouteMapData[] = [$route->getResource(), $variableNames];
    }

    /**
     * @inheritDoc
     */
    protected function dispatchVariableRoute(array $scopedVariableRouteMapExpressions, string $uri): array
    {
        foreach ($scopedVariableRouteMapExpressions as $regex) {
            if (!preg_match($regex, $uri, $matches)) {
                continue;
            }

            [$resource, $variableNames] = $this->variableRouteMapData[$matches['MARK']];
            $variableData = [];

            foreach ($variableNames as $index => &$variableName) {
                $variableData[$variableName] = &$matches[$index + 1];
            }

            unset($variableNames, $variableName);

            return [self::FOUND, $resource, $variableData];
        }

        return [self::NOT_FOUND];
    }
}
