<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

use Fig\Http\Message\RequestMethodInterface;
use Rosem\Contract\Route\InvalidRouteExceptionInterface;

/**
 * Partial implementation of the route collector interface.
 */
abstract class AbstractRouteCollector implements RouteCollectorInterface
{
    /**
     * Adds a GET route to the collection
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function get(string $route, $handler): RouteInterface
    {
        return $this->addRoute(RequestMethodInterface::METHOD_GET, $route, $handler);
    }

    /**
     * Adds a POST route to the collection
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function post(string $route, $handler): RouteInterface
    {
        return $this->addRoute(RequestMethodInterface::METHOD_POST, $route, $handler);
    }

    /**
     * Adds a PUT route to the collection
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function put(string $route, $handler): RouteInterface
    {
        return $this->addRoute(RequestMethodInterface::METHOD_PUT, $route, $handler);
    }

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function delete(string $route, $handler): RouteInterface
    {
        return $this->addRoute(RequestMethodInterface::METHOD_DELETE, $route, $handler);
    }

    /**
     * Adds a PATCH route to the collection
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     *
     * @return  RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function patch(string $route, $handler): RouteInterface
    {
        return $this->addRoute(RequestMethodInterface::METHOD_PATCH, $route, $handler);
    }

    /**
     * Adds a HEAD route to the collection
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     *
     * @return RouteInterface
     * @throws InvalidRouteExceptionInterface
     */
    public function head(string $route, $handler): RouteInterface
    {
        return $this->addRoute(RequestMethodInterface::METHOD_HEAD, $route, $handler);
    }
}
