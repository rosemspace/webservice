<?php

namespace Rosem\Component\Route\Contract;

interface RouteParserInterface
{
    public function parse(string $routePattern): array;
}
