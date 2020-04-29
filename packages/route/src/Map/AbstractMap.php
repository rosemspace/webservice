<?php

namespace Rosem\Component\Route\Map;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Rosem\Component\Route\Contract\{
    RouteDispatcherInterface,
    RouteParserInterface
};
use Rosem\Component\Route\RouteParser;
use Rosem\Contract\Route\RouteCollectorInterface;

abstract class AbstractMap implements RouteCollectorInterface, RouteDispatcherInterface
{
    /**
     * Route pattern parser.
     *
     * @var RouteParserInterface
     */
    protected RouteParserInterface $parser;

    /**
     * Data of each static route in the collection.
     *
     * @var array[]
     */
    protected array $staticRouteMap = [];

    /**
     * Data of each variable route in the collection.
     *
     * @var array
     */
    protected array $variableRouteMap = [];

    /**
     * @var string
     */
    protected string $currentGroupPrefix = '';

    /**
     * A delimiter which is used for separation of route parts.
     *
     * @var string
     */
    private string $delimiter = '/';

    /**
     * Determine if a trailing delimiter should be kept.
     *
     * @var bool
     */
    private bool $keepTrailingDelimiter = false;

    /**
     * UTF-8 flag.
     *
     * @var bool
     */
    protected bool $utf8 = false;

    /**
     * AbstractRouteMap constructor.
     *
     * @param RouteParserInterface $parser
     */
    public function __construct(RouteParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Use UTF-8 flag or not.
     *
     * @param bool $use
     */
    public function useUtf8(bool $use = true): void
    {
        $this->utf8 = $use;
    }

    /**
     * @inheritDoc
     */
    public function addRoute($scopes, string $routePattern, $data): void
    {
        $scopes = (array)$scopes;
        $routePattern = $this->currentGroupPrefix . $this->normalize($routePattern);

        foreach ($this->parser->parse($routePattern) as $parsedRoute) {
            if ($this->isStaticRoute($parsedRoute)) {
                foreach ($scopes as $scope) {
                    $this->addStaticRoute($scope, $routePattern, $data);
                }
            } else {
                foreach ($scopes as $scope) {
                    $this->addVariableRoute($scope, [$routePattern, ...$parsedRoute], $data);
                }
            }
        }
    }

    private function normalize(string $route): string
    {
        return $this->keepTrailingDelimiter ? $route : rtrim($route, $this->delimiter);
    }

    /**
     * @param string   $prefix
     * @param callable $callback
     *
     * @return void
     */
    public function addGroup(string $prefix, callable $callback): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix .= $prefix;
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
    }

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
     *         $data,
     *         ['varName' => 'value', other variables...]
     *     ]
     *
     * @see \Fig\Http\Message\RequestMethodInterface
     * @see \Fig\Http\Message\StatusCodeInterface
     */
    public function dispatch(string $scope, string $uri): array
    {
        if (isset($this->staticRouteMap[$uri])) {
            $routeData = $this->staticRouteMap[$uri];

            if (isset($routeData[$scope])) {
                return [StatusCode::STATUS_OK, $routeData[$scope], []];
            }

            // If there are no allowed methods the route simply does not exist
            return [StatusCode::STATUS_METHOD_NOT_ALLOWED, array_keys($routeData)];
        }

        if (isset($this->variableRouteMapExpressions[$scope])) {
            $routeData = $this->dispatchVariableRoute($this->variableRouteMapExpressions[$scope], $uri);

            if ($routeData[0] === StatusCode::STATUS_OK) {
                return $routeData;
            }
        }

        // Find allowed methods for this URI by matching against all other HTTP methods as well
        $allowedScopes = [];

        foreach ($this->variableRouteMapExpressions as $allowedScope => $metaData) {
            if ($scope === $allowedScope) {
                continue;
            }

            $routeData = $this->dispatchVariableRoute($metaData, $uri);

            if ($routeData[0] !== StatusCode::STATUS_OK) {
                continue;
            }

            $allowedScopes[] = $allowedScope;
        }

        // If there are no allowed methods the route simply does not exist
        if ($allowedScopes !== []) {
            return [StatusCode::STATUS_METHOD_NOT_ALLOWED, $allowedScopes];
        }

        return [StatusCode::STATUS_NOT_FOUND];
    }

    /**
     * Check if the given parsed route is a static route.
     *
     * @param array $parsedRoute
     *
     * @return bool
     */
    private function isStaticRoute(array $parsedRoute): bool
    {
        return $parsedRoute[RouteParser::KEY_VARIABLES_NAMES] === [];
    }

    private function addStaticRoute($scope, string $routePattern, $data): void
    {
        if (isset($this->staticRouteMap[$routePattern][$scope])) {
            // todo required: throw an error for the same scope
        }

        // todo optimization: add reference if data is same
        $this->staticRouteMap[$routePattern][$scope] = $data;
    }

    abstract protected function addVariableRoute(string $scope, array $parsedRoute, $data): void;

    abstract protected function dispatchVariableRoute(array $metaData, string $uri): array;
}
