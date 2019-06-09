<?php

namespace Rosem\Component\Route;

interface CompilerInterface
{
    public function compile(array $methods, string $routePattern, string $handler): RegexRouteInterface;
}
