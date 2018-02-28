<?php

namespace Rosem\RouteCollector;

use FastRoute\Dispatcher;
use Psrnext\RouteCollector\RouteDispatcherInterface;

class RouteDispatcher implements RouteDispatcherInterface
{
    /**
     * @var Dispatcher
     */
    protected $driver;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->driver = $dispatcher;
    }

    /**
     * Dispatches against the provided HTTP method verb and URI.
     * Returns array with one of the following formats:
     *     [self::NOT_FOUND]
     *     [self::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [self::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array
     */
    public function dispatch(string $httpMethod, string $uri) : array
    {
        return $this->driver->dispatch($httpMethod, $uri);
    }
}
