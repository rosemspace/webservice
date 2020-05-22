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
    protected function saveVariableRoute(string $scope, array $parsedRoute, $resource): void
    {
        // (*:n) - shorthand for (*MARK:n)
        $parsedRoute[1] .= "(*:$this->variableRouteCount)";
        $this->addVariableRouteRegex($parsedRoute);
        $this->variableRouteMapExpressions[$scope][count($this->variableRouteMapExpressions[$scope]) - 1] =
            $this->variableRouteRegex;
        // TODO: data adding strategy / scope functionality?
        $this->variableRouteMap[] = [$resource, $parsedRoute[2]];
    }

    /**
     * @inheritDoc
     */
    protected function dispatchVariableRoute(array $variableRouteMapExpressions, string $uri): array
    {
        foreach ($variableRouteMapExpressions as $regExp) {
            if (!preg_match($regExp, $uri, $matches)) {
                continue;
            }

            [$resource, $variableNames] = $this->variableRouteMap[$matches['MARK']];
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
