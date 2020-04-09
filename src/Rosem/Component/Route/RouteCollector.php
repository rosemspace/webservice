<?php

namespace Rosem\Component\Route;

use Rosem\Contract\Route\{
    AbstractRouteCollector,
    RouteGroupInterface,
    RouteInterface
};

use function count;

class RouteCollector extends AbstractRouteCollector
{
    use RouteMapTrait;

    /**
     * @var CompilerInterface
     */
    protected CompilerInterface $compiler;

    /**
     * @var string
     */
    protected string $currentGroupPrefix = '';

    /**
     * RouteCollector constructor.
     *
     * @param CompilerInterface                $compiler
     * @param RegexBasedDataGeneratorInterface $dataGenerator
     */
    public function __construct(CompilerInterface $compiler, RegexBasedDataGeneratorInterface $dataGenerator)
    {
        $this->compiler = $compiler;
        $this->variableRouteMap = $dataGenerator;
    }

    protected static function normalize(string $route): string
    {
        return $route;
//        return rtrim($route, '/');
//        return '/' . trim($route, '/');
    }

    /**
     * @param string|string[] $methods
     * @param string          $pattern
     * @param mixed           $handler
     *
     * @return RouteInterface
     * @throws Exception\TooLongRouteException
     */
    public function addRoute($methods, string $pattern, $handler): RouteInterface
    {
        $routes = $this->compiler->compile(
            (array)$methods,
            $this->currentGroupPrefix . self::normalize($pattern),
            $handler
        );

        foreach ($routes as $route) {
            if (count($route->getVariableNames())) {
                try {
                    $this->variableRouteMap->addRoute($route);
                } catch (Exception\TooLongRouteException $exception) {
                    // TODO: add rollback
                    //$this->variableRouteMap[$method]->rollback();
                    $this->variableRouteMap->newChunk();
                    $this->variableRouteMap->addRoute($route);
                }
            } else {
                $middleware = &$route->getMiddlewareExtensions();
                $this->staticRouteMap[$pattern] = [$route->getMethods(), $route->getHandler(), &$middleware];
            }
        }

        // TODO: return middleware collector?
        return $routes[0];
    }

    /**
     * @param string   $prefix
     * @param callable $callback
     *
     * @return RouteGroupInterface
     */
    public function addGroup(string $prefix, callable $callback): RouteGroupInterface
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix .= self::normalize($prefix);
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
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
     * @return RegexBasedDataGeneratorInterface
     */
    public function getVariableRouteMap(): RegexBasedDataGeneratorInterface
    {
        return $this->variableRouteMap;
    }
}
