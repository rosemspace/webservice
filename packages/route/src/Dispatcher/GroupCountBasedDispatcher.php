<?php

namespace Rosem\Component\Route\Dispatcher;

use Rosem\Component\Route\Contract\RegexBasedDispatcherInterface;
use Rosem\Component\Route\DataGenerator\GroupCountBasedDataGenerator;

use function count;
use function preg_match;

class GroupCountBasedDispatcher implements RegexBasedDispatcherInterface
{
    /**
     * @inheritDoc
     */
    public function dispatch(array $metaList, array $dataList, string $uri): array
    {
        foreach ($metaList as &$meta) {
            if (!preg_match($meta[GroupCountBasedDataGenerator::KEY_REGEX], $uri, $matches)) {
                continue;
            }

            $routeData = $dataList[count($matches) + $meta[GroupCountBasedDataGenerator::KEY_OFFSET]];
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
