<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Psr\Route;

/**
 * Provides meta information of a generic route.
 */
interface RouteGroupInterface
{
    /**
     * Sets the middleware logic to be executed before the route will be resolved.
     *
     * @param string $middleware
     * @param array  $options
     *
     * @return RouteGroupInterface
     * @see \Psr\Http\Server\MiddlewareInterface
     */
    public function addMiddleware(string $middleware, array $options = []): RouteGroupInterface;
}
