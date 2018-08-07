<?php

namespace Rosem\Route;

interface CompilerInterface
{
    public function compile(array $methods, string $routePattern, string $handler): RegexRouteInterface;
}
