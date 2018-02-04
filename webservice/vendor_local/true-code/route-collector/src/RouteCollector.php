<?php

namespace TrueCode\RouteCollector;

use FastRoute\DataGenerator;
use FastRoute\RouteParser;
use Psrnext\RouteCollector\RouteCollectorInterface;

class RouteCollector implements RouteCollectorInterface
{
    protected $routeParser;
    protected $dataGenerator;
    protected $currentGroupPrefix;

    /**
     * Constructs a route collector.
     *
     * @param RouteParser   $routeParser
     * @param DataGenerator $dataGenerator
     */
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator) {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->currentGroupPrefix = '';
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function addRoute($httpMethod, string $route, ...$handlers) : void {
        $route = $this->currentGroupPrefix . $route;
        $routeDataList = $this->routeParser->parse($route);

        foreach ((array) $httpMethod as $method) {
            foreach ($routeDataList as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, $handlers);
            }
        }
    }

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup(string $prefix, callable $callback) : void {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function get(string $route, ...$handlers) : void {
        $this->addRoute(self::GET, $route, ...$handlers);
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function post(string $route, ...$handlers) : void {
        $this->addRoute(self::POST, $route, ...$handlers);
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function put(string $route, ...$handlers) : void {
        $this->addRoute(self::PUT, $route, ...$handlers);
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function delete(string $route, ...$handlers) : void {
        $this->addRoute(self::DELETE, $route, ...$handlers);
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function patch(string $route, ...$handlers) : void {
        $this->addRoute(self::PATCH, $route, ...$handlers);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     */
    public function head(string $route, ...$handlers) : void {
        $this->addRoute(self::HEAD, $route, ...$handlers);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData() {
        return $this->dataGenerator->getData();
    }
}
