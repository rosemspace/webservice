<?php

namespace Rosem\Component\Route\Map;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

class MarkBasedMap extends AbstractRegexBasedMap
{
    public const KEY_VARIABLES = 0;

    public const KEY_ROUTE_DATA = 1;

    /**
     * @inheritDoc
     */
    protected function addSingleVariableRoute(string $scope, array $parsedRoute, $data): void
    {
        [$routePattern, $regex, $variableNames] = $parsedRoute;
        // (*:n) - shorthand for (*MARK:n)
        $this->addVariableRouteRegex($routePattern, $regex . '(*:' . $this->variableRouteCount . ')');
        $this->variableRouteMapExpressions[$scope][count($this->variableRouteMapExpressions[$scope]) - 1] =
            '~^' . $this->variableRouteRegex . '$~' . ($this->utf8 ? 'u' : '');
        // TODO: data adding strategy / scope functionality?
        $this->variableRouteMap[] = [$data, $variableNames];
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

            [$data, $variableNames] = $this->variableRouteMap[$matches['MARK']];
            $variableData = [];

            foreach ($variableNames as $index => &$variableName) {
                $variableData[$variableName] = &$matches[$index + 1];
            }

            unset($variableNames, $variableName);

            return [StatusCode::STATUS_OK, $data, $variableData];
        }

        unset($regex);

        return [StatusCode::STATUS_NOT_FOUND];
    }

    /**
     * Prepare internal data for the new chunk.
     *
     * @param string $scope
     *
     * @return void
     */
    protected function createNewVariableRouteChunk(string $scope): void
    {
        $this->variableRouteRegexTree->clear();
        $this->variableRouteMapExpressions[$scope][] = '';
    }
}
