<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Map;

use Rosem\Component\Route\RouteParser;

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
        [$routePattern, $regex, $variableNames] = $parsedRoute;
        // (*:n) - shorthand for (*MARK:n)
        $this->addVariableRouteRegex($routePattern, $regex . '(*:' . $this->variableRouteCount . ')');
        $this->variableRouteMapExpressions[$scope][count($this->variableRouteMapExpressions[$scope]) - 1] =
            RouteParser::REGEXP_DELIMITER . '^' . $this->variableRouteRegex . '$' . RouteParser::REGEXP_DELIMITER .
            'sD' . ($this->utf8 ? 'u' : '');
        // TODO: data adding strategy / scope functionality?
        $this->variableRouteMap[] = [$resource, $variableNames];
    }

    /**
     * @inheritDoc
     */
    protected function dispatchVariableRoute(array $variableRouteMapExpressions, string $uri): array
    {
        foreach ($variableRouteMapExpressions as &$regex) {
            if (!preg_match($regex, $uri, $matches)) {
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

        unset($regex);

        return [self::NOT_FOUND];
    }
}
