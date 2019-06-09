<?php

namespace Rosem\Component\Route\Dispatcher;

class MarkBasedDispatcher extends AbstractRegexBasedDispatcher
{
    /**
     * @param array  $metaList
     * @param array  $dataList
     * @param string $uri
     *
     * @return array
     */
    public function dispatch(array &$metaList, array &$dataList, string &$uri): array
    {
        foreach ($metaList as &$regex) {
            if (preg_match($regex, $uri, $matches)) {
                [$handler, $middleware, $variableNames] = $dataList[$matches['MARK']];
                $variableData = [];

                /** @var string[] $variableNames */
                foreach ($variableNames as $index => &$variableName) {
                    $variableData[$variableName] = &$matches[$index + 1];
                }

                return [self::ROUTE_FOUND, $handler, $middleware, $variableData];
            }
        }

        return [self::ROUTE_NOT_FOUND];
    }
}
