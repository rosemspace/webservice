<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Dispatcher;

use Rosem\Component\Route\Contract\RegexBasedDispatcherInterface;

use function preg_match;

class MarkBasedDispatcher implements RegexBasedDispatcherInterface
{
    public function dispatch(array $metaList, array $dataList, string $uri): array
    {
        foreach ($metaList as &$regex) {
            if (! preg_match($regex, $uri, $matches)) {
                continue;
            }

            $routeData = $dataList[$matches['MARK']];
            $variableData = [];

            // count($routeData) - 1 = 3
            /** @noinspection AlterInForeachInspection */
            foreach ($routeData[3] as $index => &$variableName) {
                $variableData[$variableName] = &$matches[$index + 1];
            }

            $routeData[3] = $variableData;
            unset($variableData, $variableName);

            return $routeData;
        }

        return [];
    }
}
