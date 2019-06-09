<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

use Rosem\Contract\Route\InvalidRouteExceptionInterface;

/**
 * Route collector collects routes with its handlers.
 */
interface RouteCollectorInterface
{
    /**
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethods
     * @param string          $routePattern
     * @param mixed           $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function addRoute($httpMethods, string $routePattern, $handler): RouteInterface;

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string   $prefix
     * @param callable $callback
     *
     * @return RouteGroupInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function addGroup(string $prefix, callable $callback): RouteGroupInterface;

    /**
     * Adds a GET route to the collection
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function get(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a POST route to the collection
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function post(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a PUT route to the collection
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function put(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function delete(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a PATCH route to the collection
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function patch(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a HEAD route to the collection
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function head(string $routePattern, $handler): RouteInterface;
}
