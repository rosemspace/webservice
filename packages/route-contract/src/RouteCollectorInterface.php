<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

/**
 * Route collector collects routes with its data.
 */
interface RouteCollectorInterface
{
    /**
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $scopes
     * @param string          $routePattern
     * @param mixed           $resource
     *
     * @return void
     * @throws BadRouteExceptionInterface
     */
    public function addRoute($scopes, string $routePattern, $resource): void;

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string   $prefix
     * @param callable $callback
     *
     * @return void
     * @throws BadRouteExceptionInterface
     */
    public function addGroup(string $prefix, callable $callback): void;
}
