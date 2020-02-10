<?php

namespace Rosem\Component\Route;

use Rosem\Contract\Route\RouteInterface;

interface RegexRouteInterface extends RouteInterface
{
    public function getRegex(): string;

    public function getVariableNames(): array;

    //todo
    public function &getMiddlewareExtensions(): array;
}
