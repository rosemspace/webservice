<?php

namespace Rosem\Route;

use Rosem\Psr\Route\RouteInterface as StandardRouteInterface;

interface RouteInterface extends StandardRouteInterface
{
    public function getRegex(): string;

    public function getVariableNames(): array;

    public function &getMiddlewareListReference(): array;
}
