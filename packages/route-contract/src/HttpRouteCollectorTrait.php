<?php

declare(strict_types=1);

/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;

/**
 * Partial implementation of the route collector interface.
 */
trait HttpRouteCollectorTrait
{
    abstract public function addRoute($httpMethods, string $routePattern, $handler): void;

    public function get(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_GET, $routePattern, $handler);
    }

    public function post(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_POST, $routePattern, $handler);
    }

    public function put(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_PUT, $routePattern, $handler);
    }

    public function patch(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_PATCH, $routePattern, $handler);
    }

    public function delete(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_DELETE, $routePattern, $handler);
    }

    public function head(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_HEAD, $routePattern, $handler);
    }

    public function options(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_OPTIONS, $routePattern, $handler);
    }

    public function purge(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_PURGE, $routePattern, $handler);
    }

    public function trace(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_TRACE, $routePattern, $handler);
    }

    public function connect(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_CONNECT, $routePattern, $handler);
    }

    public function any(string $routePattern, $handler): void
    {
        $this->addRoute(
            [
                RequestMethod::METHOD_HEAD,
                RequestMethod::METHOD_GET,
                RequestMethod::METHOD_POST,
                RequestMethod::METHOD_PUT,
                RequestMethod::METHOD_PATCH,
                RequestMethod::METHOD_DELETE,
                RequestMethod::METHOD_PURGE,
                RequestMethod::METHOD_OPTIONS,
                RequestMethod::METHOD_TRACE,
                RequestMethod::METHOD_CONNECT,
            ],
            $routePattern,
            $handler
        );
    }

    public function redirect(): void
    {
        //todo $httpMethods, string $routePattern, string $location, int $statusCode
    }
}
