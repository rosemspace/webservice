<?php

namespace Rosem\Route\Dispatcher;

use Rosem\Route\DataGenerator\GroupCountBasedDataGenerator;
use function count;

class GroupCountBasedDispatcher extends AbstractRegexBasedDispatcher
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
        foreach ($metaList as &$meta) {
            if (!preg_match($meta[GroupCountBasedDataGenerator::KEY_REGEX], $uri, $matches)) {
                continue;
            }

            [
                $handler,
                $middleware,
                $variableNames,
            ] = $dataList[count($matches) + $meta[GroupCountBasedDataGenerator::KEY_OFFSET]];
            $variableData = [];

            /** @var string[] $variableNames */
            foreach ($variableNames as $index => &$variableName) {
                $variableData[$variableName] = &$matches[$index + 1];
            }

            return [self::ROUTE_FOUND, $handler, $middleware, $variableData];
        }

        return [self::ROUTE_NOT_FOUND];
    }
}
