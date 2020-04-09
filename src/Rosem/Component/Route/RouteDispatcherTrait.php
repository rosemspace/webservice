<?php

namespace Rosem\Component\Route;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

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
     * {@inheritDoc}
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
     *         ['varName' => 'value', other variables...]
     *     ]
     * @see \Fig\Http\Message\RequestMethodInterface
     * @see \Fig\Http\Message\StatusCodeInterface
     */
    public function dispatch(string $method, string $uri): array
    {
        if (isset($this->staticRouteMap[$uri])) {
            $routeData = $this->staticRouteMap[$uri];
            // There is no any variables in static routes
            $routeData[] = [];
        } else {
            $routeData = $this->dispatcher->dispatch(
                $this->variableRouteMap->routeExpressions ?? [],
                $this->variableRouteMap->routeData ?? [],
                $uri
            );
        }

        if (!$routeData) {
            return [StatusCode::STATUS_NOT_FOUND];
        }

        // If there are no allowed methods the route simply does not exist
        if (!in_array($method, $routeData[0], true)) {
            return [StatusCode::STATUS_METHOD_NOT_ALLOWED, $routeData[0]];
        }

        $routeData[0] = StatusCode::STATUS_OK;

        return $routeData;
    }
}
