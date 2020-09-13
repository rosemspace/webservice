<?php

declare(strict_types=1);

/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Component\Route\Contract;

/**
 * Provides meta information of a route.
 */
interface RouteInterface
{
    /**
     * Retrieves the path pattern of the route.
     */
    public function getPathPattern(): string;

    /**
     * Retrieves the host pattern of the route.
     */
    public function getHostPattern(): string;

    /**
     * Retrieves the scheme pattern of the route.
     */
    public function getSchemePattern(): string;

    /**
     * Retrieves the scope of the route.
     *
     * @return string Returns the route scope.
     */
    public function getScope(): string;

    /**
     * Retrieves the server request handler.
     *
     * @return mixed
     * @see \Psr\Http\Server\RequestHandlerInterface
     */
    public function getResource();

    public function getMeta(): array;

    /**
     * Tests whether this route matches the given URI.
     */
    public function matches(string $uri): bool;
}
