<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

/**
 * Provides meta information of a route.
 */
interface RouteInterface
{
    /**
     * Retrieves the path pattern of the route.
     *
     * @return string
     */
    public function getPathPattern(): string;

    /**
     * Retrieves the host pattern of the route.
     *
     * @return string
     */
    public function getHostPattern(): string;

    /**
     * Retrieves the scheme pattern of the route.
     *
     * @return string
     */
    public function getSchemePattern(): string;

    /**
     * Retrieves the HTTP methods of the route.
     *
     * @return string[] Returns the route methods.
     */
    public function getMethods(): array;

    /**
     * Retrieves the server request handler.
     *
     * @return string
     * @see \Psr\Http\Server\RequestHandlerInterface
     */
    public function getHandler(): string;

    public function getVariableNames(): array;

    /**
     * Sets the middleware logic to be executed before the route will be resolved.
     *
     * @param callable $middlewareExtension
     *
     * @return RouteInterface
     * @see \Psr\Http\Server\MiddlewareInterface
     */
    public function addMiddleware(callable $middlewareExtension): RouteInterface;
}
