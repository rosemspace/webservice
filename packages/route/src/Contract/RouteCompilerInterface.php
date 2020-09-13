<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Contract;

use Rosem\Contract\Route\RouteInterface;

/**
 * Interface CompilerInterface.
 */
interface RouteCompilerInterface
{
    /**
     * Compile the pattern to real routes.
     *
     * @return RouteInterface[]
     */
    public function compile(array $methods, string $routePattern, string $handler): array;
}
