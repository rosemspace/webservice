<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

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
     * Adds a HEAD route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('HEAD', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function head(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a GET route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('GET', $routePattern, $handler)
     * </code>
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
     * This is simply an alias of
     * <code>
     * $this->addRoute('POST', $routePattern, $handler)
     * </code>
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
     * This is simply an alias of
     * <code>
     * $this->addRoute('PUT', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function put(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a PATCH route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('PATCH', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function patch(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('DELETE', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function delete(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a PURGE route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('PURGE', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function purge(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a OPTIONS route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('OPTIONS', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function options(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a TRACE route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('TRACE', $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function trace(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a CONNECT route to the collection
     * This is simply an alias of $this->addRoute('CONNECT', $routePattern, $handler)
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function connect(string $routePattern, $handler): RouteInterface;

    /**
     * Adds a HEAD, GET, POST, PUT, PATCH, DELETE, PURGE, OPTIONS, TRACE and CONNECT routes to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute([
     *     'HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'PURGE', 'OPTIONS', 'TRACE', 'CONNECT'
     * ], $routePattern, $handler)
     * </code>
     *
     * @param string $routePattern
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function any(string $routePattern, $handler): RouteInterface;
}
