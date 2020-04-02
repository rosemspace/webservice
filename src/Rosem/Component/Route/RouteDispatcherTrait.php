<?php

namespace Rosem\Component\Route;

/**
 * Trait RouteDispatcherTrait.
 */
trait RouteDispatcherTrait
{
    /**
     * @var RegexBasedDispatcherInterface
     */
    protected RegexBasedDispatcherInterface $dispatcher;

    /**
     * Dispatches against the provided HTTP method verb and URI.
     * Returns array with one of the following formats:
     *     [
     *         StatusCodeInterface::STATUS_NOT_FOUND
     *     ]
     *     [
     *         StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED,
     *         [RequestMethodInterface::METHOD_GET, other allowed methods...]
     *     ]
     *     [
     *         StatusCodeInterface::STATUS_FOUND,
     *         $handler,
     *         [$middleware1, $middleware2, ...],
     *         ['varName' => 'value', other variables...]
     *     ]
     *
     * @see \Fig\Http\Message\RequestMethodInterface
     * @see \Fig\Http\Message\StatusCodeInterface
     *
     * @param string $method
     * @param string $uri
     *
     * @return array The handler and variables
     */
    public function dispatch(string $method, string $uri): array
    {
        if (isset($this->staticRouteMap[$method][$uri])) {
            [$handler, $middleware] = $this->staticRouteMap[$method][$uri];

            return [200, &$handler, &$middleware, []];
        }

        return $this->dispatcher->dispatch(
            $this->variableRouteMap[$method]->routeExpressions ?? [],
            $this->variableRouteMap[$method]->routeData ?? [],
            $uri
        );
    }
}
