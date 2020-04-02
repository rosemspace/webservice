<?php

namespace Rosem\Component\Route\Dispatcher;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Rosem\Component\Route\{
    DataGenerator\StringNumberBasedDataGenerator,
    RegexBasedDispatcherInterface
};

class StringNumberBasedDispatcher implements RegexBasedDispatcherInterface
{
    /**
     * @param array[] $metaList
     * @param array   $dataList
     * @param string  $uri
     *
     * @return array
     */
    public function dispatch(array $metaList, array $dataList, string $uri): array
    {
        foreach ($metaList as &$meta) {
            if (!preg_match(
                $meta[StringNumberBasedDataGenerator::KEY_REGEX],
                $uri . $meta[StringNumberBasedDataGenerator::KEY_SUFFIX],
                $matches
            )) {
                continue;
            }

            unset($matches[0]);
            $segmentCount = $meta[StringNumberBasedDataGenerator::KEY_SEGMENT_COUNT];
            $indexString = '';

            // todo fix a bug
            do {
                $lastMatch = array_pop($matches);
                $indexString = $lastMatch[0] . $indexString . (isset($lastMatch[1]) ? $lastMatch[-1] : '');
            } while (--$segmentCount);

            [$handler, $middleware, $variableNames] =
                $dataList[$meta[StringNumberBasedDataGenerator::KEY_LAST_CHUNK_OFFSET] + (int)$indexString];

            return [StatusCode::STATUS_FOUND, $handler, $middleware, array_combine($variableNames, $matches)];
        }

        return [StatusCode::STATUS_NOT_FOUND];
    }
}
