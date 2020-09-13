<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Contract;

interface RouteParserInterface
{
    public function parse(string $routePattern): array;
}
