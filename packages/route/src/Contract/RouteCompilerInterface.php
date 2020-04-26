<?php

namespace Rosem\Component\Route\Contract;

/**
 * Interface CompilerInterface.
 */
interface RouteCompilerInterface
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
