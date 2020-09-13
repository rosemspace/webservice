<?php

declare(strict_types=1);

/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

/**
 * Route collector collects routes with its handlers.
 *
 * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5.1.1
 */
interface HttpRouteCollectorInterface extends RouteCollectorInterface
{
    /**
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethods
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function addRoute($httpMethods, string $routePattern, $handler): void;

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @throws BadRouteExceptionInterface
     */
    public function addGroup(string $prefix, callable $callback): void;

    /**
     * Adds a GET route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('GET', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function get(string $routePattern, $handler): void;

    /**
     * Adds a POST route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('POST', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function post(string $routePattern, $handler): void;

    /**
     * Adds a PUT route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('PUT', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function put(string $routePattern, $handler): void;

    /**
     * Adds a PATCH route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('PATCH', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function patch(string $routePattern, $handler): void;

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('DELETE', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function delete(string $routePattern, $handler): void;

    /**
     * Adds a HEAD route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('HEAD', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function head(string $routePattern, $handler): void;

    /**
     * Adds a OPTIONS route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('OPTIONS', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function options(string $routePattern, $handler): void;

    /**
     * Adds a PURGE route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('PURGE', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function purge(string $routePattern, $handler): void;

    /**
     * Adds a TRACE route to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute('TRACE', $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function trace(string $routePattern, $handler): void;

    /**
     * Adds a CONNECT route to the collection
     * This is simply an alias of $this->addRoute('CONNECT', $routePattern, $handler)
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function connect(string $routePattern, $handler): void;

    /**
     * Adds a HEAD, GET, POST, PUT, PATCH, DELETE, PURGE, OPTIONS, TRACE and CONNECT routes to the collection
     * This is simply an alias of
     * <code>
     * $this->addRoute([
     *     'HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'PURGE', 'OPTIONS', 'TRACE', 'CONNECT'
     * ], $routePattern, $handler)
     * </code>
     *
     * @param mixed $handler
     *
     * @throws BadRouteExceptionInterface
     */
    public function any(string $routePattern, $handler): void;
}
