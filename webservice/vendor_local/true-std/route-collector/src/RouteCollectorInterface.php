<?php

namespace TrueStd\RouteCollector;

interface RouteCollectorInterface
{
    /**
     * GET method.
     */
    const GET = 'GET';

    /**
     * POST method.
     */
    const POST = 'POST';

    /**
     * PUT method.
     */
    const PUT = 'PUT';

    /**
     * DELETE method.
     */
    const DELETE = 'DELETE';

    /**
     * PATCH method.
     */
    const PATCH = 'PATCH';

    /**
     * HEAD method.
     */
    const HEAD = 'HEAD';

    /**
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string          $route
     * @param mixed           ...$handlers
     *
     * @return void
     */
    public function addRoute($httpMethod, string $route, ...$handlers) : void;

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string   $prefix
     * @param callable $callback
     *
     * @return void
     */
    public function addGroup(string $prefix, callable $callback) : void;

    /**
     * Adds a GET route to the collection
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     *
     * @return void
     */
    public function get(string $route, ...$handlers) : void;

    /**
     * Adds a POST route to the collection
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     *
     * @return void
     */
    public function post(string $route, ...$handlers) : void;

    /**
     * Adds a PUT route to the collection
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     *
     * @return void
     */
    public function put(string $route, ...$handlers) : void;

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  ...$handlers
     *
     * @return void
     */
    public function delete(string $route, ...$handlers) : void;
}
