<?php

namespace Rosem\Component\Route;

interface RegexBasedDispatcherInterface
{
    public function dispatch(array $metaList, array $dataList, string $uri): array;
}
