<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Map;

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
    protected function saveVariableRoute(string $scope, string $routePattern, $resource, array $meta): void
    {
        // (*:n) - shorthand for (*MARK:n)
        $meta[0] .= "(*:$this->variableRouteCount)";
        $this->addVariableRouteRegex($routePattern, $meta);
        $this->variableRouteMapExpressions[$scope][count($this->variableRouteMapExpressions[$scope]) - 1] =
            $this->variableRouteRegex;
        // TODO: data adding strategy / scope functionality?
        $this->variableRouteMapData[] = [$resource, $meta[1]];
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
