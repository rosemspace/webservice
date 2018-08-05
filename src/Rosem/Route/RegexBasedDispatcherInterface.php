<?php

namespace Rosem\Route;

interface RegexBasedDispatcherInterface
{
    public function dispatch(array &$metaList, array &$dataList, string &$uri): array;
}
