<?php

namespace Rosem\Route;

use Rosem\Psr\Route\RouteInterface;

interface RegexRouteInterface extends RouteInterface
{
    public function getRegex(): string;

    public function getVariableNames(): array;

    public function &getMiddlewareExtensions(): array;
}
