<?php

namespace Rosem\Component\Route\Dispatcher;

use Fig\Http\Message\StatusCodeInterface;
use Rosem\Component\Route\RegexBasedDispatcherInterface;

class MarkBasedDispatcher implements RegexBasedDispatcherInterface
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

                return [StatusCodeInterface::STATUS_FOUND, $handler, $middleware, $variableData];
            }
        }

        return [StatusCodeInterface::STATUS_NOT_FOUND];
    }
}
