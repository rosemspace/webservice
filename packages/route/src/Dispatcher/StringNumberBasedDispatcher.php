<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Dispatcher;

use Rosem\Component\Route\DataGenerator\StringNumberBasedDataGenerator;
use Rosem\Component\Route\RegexBasedDispatcherInterface;

class StringNumberBasedDispatcher implements RegexBasedDispatcherInterface
{
    /**
     * @param array[] $metaList
     */
    public function dispatch(array $metaList, array $dataList, string $uri): array
    {
        foreach ($metaList as &$meta) {
            if (! preg_match(
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

            [$methods, $handler, $middleware, $variableNames] =
                $dataList[$meta[StringNumberBasedDataGenerator::KEY_LAST_CHUNK_OFFSET] + (int) $indexString];

            return [$methods, $handler, $middleware, array_combine($variableNames, $matches)];
        }

        return [];
    }
}
