<?php
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
    /**
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethods
     * @param string          $routePattern
     * @param mixed           $handler
     *
     * @return void
     * @throws BadRouteExceptionInterface
     */
    abstract public function addRoute($httpMethods, string $routePattern, $handler): void;

    /**
     * @inheritDoc
     */
    public function head(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_HEAD, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function get(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_GET, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_POST, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_PUT, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_PATCH, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_DELETE, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function purge(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_PURGE, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function options(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_OPTIONS, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function trace(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_TRACE, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
    public function connect(string $routePattern, $handler): void
    {
        $this->addRoute(RequestMethod::METHOD_CONNECT, $routePattern, $handler);
    }

    /**
     * @inheritDoc
     */
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

    public function redirect(string $routePattern, string $route, int $statusCode): void
    {
        //todo
//        $response->withStatus(StatusCode::STATUS_FOUND)
//            ->withHeader('Location', $this->loggedInUri);
    }
}
