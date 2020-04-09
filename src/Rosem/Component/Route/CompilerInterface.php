<?php

namespace Rosem\Component\Route;

/**
 * Interface CompilerInterface.
 */
interface CompilerInterface
{
    /**
     * Compile the pattern to real routes.
     *
     * @param array  $methods
     * @param string $routePattern
     * @param string $handler
     *
     * @return \Rosem\Contract\Route\RouteInterface[]
     */
    public function compile(array $methods, string $routePattern, string $handler): array;
}
