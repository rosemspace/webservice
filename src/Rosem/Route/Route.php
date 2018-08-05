<?php

namespace Rosem\Route;

use Rosem\Psr\Route\RouteInterface as GenericRouteInterface;

class Route implements RouteInterface
{
    protected $methods;

    protected $handler;

    protected $pathPattern;

    protected $hostPattern;

    protected $schemes;

    protected $middlewareList = [];

    protected $regex;

    protected $variableNames;

    public function __construct(array $methods, string $handler, ...$data)
    {
        $this->methods = $methods;
        $this->handler = $handler;
        [$this->pathPattern, $this->regex, $this->variableNames] = $data;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function getVariableNames(): array
    {
        return $this->variableNames;
    }

    /**
     * Retrieves the HTTP methods of the route.
     *
     * @return string[] Returns the route methods.
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Retrieves the server request handler.
     *
     * @return string
     * @see \Psr\Http\Server\RequestHandlerInterface
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * Retrieves the path pattern of the route.
     *
     * @return string
     */
    public function getPathPattern(): string
    {
        return $this->pathPattern;
    }

    /**
     * Retrieves the host pattern of the route.
     *
     * @return string
     */
    public function getHostPattern(): string
    {
        return $this->hostPattern;
    }

    /**
     * Retrieves the scheme pattern of the route.
     *
     * @return string
     */
    public function getSchemePattern(): string
    {
        return $this->schemes;
    }

    /**
     * Sets the middleware logic to be executed before route will be resolved.
     *
     * @param string $middleware
     * @param array  $options
     *
     * @return GenericRouteInterface
     * @see \Psr\Http\Server\MiddlewareInterface
     */
    public function addMiddleware(string $middleware, array $options = []): GenericRouteInterface
    {
        $this->middlewareList[] = [$middleware, $options];

        return $this;
    }

    /**
     * Retrieves middleware list.
     *
     * @return array
     */
    public function getMiddlewareList(): array
    {
        return $this->middlewareList;
    }

    /**
     * Retrieves middleware list reference.
     *
     * @return array
     */
    public function &getMiddlewareListReference(): array
    {
        return $this->middlewareList;
    }
}
