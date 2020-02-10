<?php

namespace Rosem\Component\Route;

use Rosem\Contract\Route\RouteInterface;

class Route implements RegexRouteInterface
{
    protected array $methods;

    protected string $handler;

    protected string $pathPattern;

    protected string $hostPattern;

    protected array $schemes;

    protected array $middlewareExtensions = [];

    protected string $regex;

    protected array $variableNames;

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
     * @param callable $middlewareExtension
     *
     * @return RouteInterface
     * @see \Psr\Http\Server\MiddlewareInterface
     */
    public function middleware(callable $middlewareExtension): RouteInterface
    {
        $this->middlewareExtensions[] = $middlewareExtension;

        return $this;
    }

    /**
     * Retrieves middleware list reference.
     *
     * @return array
     */
    public function &getMiddlewareExtensions(): array
    {
        return $this->middlewareExtensions;
    }
}
