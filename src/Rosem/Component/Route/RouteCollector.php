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
     * @var RegexBasedDataGeneratorInterface
     */
    protected RegexBasedDataGeneratorInterface $dataGenerator;

    /**
     * @var string
     */
    protected string $currentGroupPrefix = '';

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
     * @param string          $pattern
     * @param mixed           $handler
     *
     * @return RouteInterface
     * @throws Exception\TooLongRouteException
     */
    public function addRoute($methods, string $pattern, $handler): RouteInterface
    {
        $route = $this->compiler->compile(
            (array)$methods,
            $this->currentGroupPrefix . self::normalize($pattern),
            $handler
        );

        foreach ($route->getMethods() as $method) {
            if (count($route->getVariableNames())) {
                // Dynamic route
                if (!isset($this->variableRouteMap[$method])) {
                    $this->variableRouteMap[$method] = clone $this->dataGenerator;
                }

                try {
                    $this->variableRouteMap[$method]->addRoute($route);
                } catch (Exception\TooLongRouteException $exception) {
                    //$this->variableRouteMap[$method]->rollback(); // TODO: add rollback
                    $this->variableRouteMap[$method]->newChunk();
                    $this->variableRouteMap[$method]->addRoute($route);
                }
            } else {
                // Static route
                if (!isset($this->staticRouteMap[$method])) {
                    $this->staticRouteMap[$method] = [];
                }

                $middleware = &$route->getMiddlewareExtensions();
                $this->staticRouteMap[$method][$pattern] = [$route->getHandler(), &$middleware];
            }
        }

        return $route;
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
     * @return array
     */
    public function getVariableRouteMap(): array
    {
        return $this->variableRouteMap;
    }
}
