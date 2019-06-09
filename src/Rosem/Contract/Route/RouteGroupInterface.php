<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

/**
 * Provides meta information of a generic route.
 */
interface RouteGroupInterface
{
    /**
     * Sets the middleware logic to be executed before the route will be resolved.
     *
     * @param callable $middlewareExtension
     *
     * @return RouteGroupInterface
     * @see \Psr\Http\Server\MiddlewareInterface
     */
    public function middleware(callable $middlewareExtension): RouteGroupInterface;
}
