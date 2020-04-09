<?php

namespace Rosem\Component\Route;

/**
 * Interface RegexBasedDispatcherInterface.
 */
interface RegexBasedDispatcherInterface
{
    /**
     * Dispatches against the provided data and URI.
     *
     * Returns array with one of the following formats:
     *     []
     *     [
     *         $methods,
     *         $handler,
     *         $middlewareExtensions,
     *         ['varName' => 'value', other variables...]
     *     ]
     *
     * @param array  $metaList
     * @param array  $dataList
     * @param string $uri
     *
     * @return array<int, mixed>
     */
    public function dispatch(array $metaList, array $dataList, string $uri): array;
}
