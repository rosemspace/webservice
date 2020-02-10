<?php

namespace Rosem\Component\Route\Dispatcher;

use Fig\Http\Message\StatusCodeInterface;
use Rosem\Component\Route\{
    DataGenerator\GroupCountBasedDataGenerator,
    RegexBasedDispatcherInterface
};

use function count;

class GroupCountBasedDispatcher implements RegexBasedDispatcherInterface
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

            return [StatusCodeInterface::STATUS_OK, $handler, $middleware, $variableData];
        }

        return [StatusCodeInterface::STATUS_NOT_FOUND];
    }
}
