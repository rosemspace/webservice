<?php

namespace Rosem\Route;

use Rosem\Psr\Route\{
    AbstractRouteCollector, RouteGroupInterface, RouteInterface
};
use function count;

class Collector extends AbstractRouteCollector
{
    use MapTrait;

    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var RegexBasedDataGeneratorInterface
     */
    protected $dataGenerator;

    /**
     * @var string
     */
    protected $prefix = '';

    public function __construct(CompilerInterface $compiler, RegexBasedDataGeneratorInterface $dataGenerator)
    {
        $this->compiler = $compiler;
        $this->dataGenerator = $dataGenerator;
    }

    protected static function normalize(string $route): string
    {
        return '/' . trim($route, '/');
    }

    /**
     * @param string|string[] $methods
     * @param string          $routePattern
     * @param mixed           $handler
     *
     * @return RouteInterface
     * @throws Exception\TooLongRouteException
     */
    public function addRoute($methods, string $routePattern, $handler): RouteInterface
    {
        $route = $this->compiler->compile((array)$methods, self::normalize($routePattern), $handler);

        foreach ($route->getMethods() as $method) {
            if (count($route->getVariableNames())) { // dynamic route
                if (!isset($this->variableRouteMap[$method])) {
                    $this->variableRouteMap[$method] = clone $this->dataGenerator;
                }

                try {
                    $this->variableRouteMap[$method]->addRoute($route);
                } catch (Exception\TooLongRouteException $exception) {
//                    $this->variableRouteMap[$method]->rollback(); // TODO: add rollback
                    $this->variableRouteMap[$method]->newChunk();
                    $this->variableRouteMap[$method]->addRoute($route);
                }
            } else { // static route
                if (!isset($this->staticRouteMap[$method])) {
                    $this->staticRouteMap[$method] = [];
                }

                $middleware = &$route->getMiddlewareListReference();
                $this->staticRouteMap[$method][$routePattern] = [$route->getHandler(), &$middleware];
            }
        }

        return $route;
    }

    /**
     * @param string   $prefix
     * @param callable $group
     *
     * @return RouteGroupInterface
     */
    public function addGroup(string $prefix, callable $group): RouteGroupInterface
    {
        $this->prefix .= self::normalize($prefix);
        $group($this);
        $this->prefix = '';
        // TODO: return
    }

    /**
     * @return array
     */
    public function getStaticRouteMap(): array
    {
        return $this->staticRouteMap;
    }

    /**
     * @return array
     */
    public function getVariableRouteMap(): array
    {
        return $this->variableRouteMap;
    }
}
