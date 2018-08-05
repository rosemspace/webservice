<?php

namespace Rosem\Route;

use Rosem\Psr\Route\RouteDispatcherInterface;

class Dispatcher implements RouteDispatcherInterface
{
    use MapTrait;
    use RegexBasedDispatcherTrait;

    /**
     * RouteDispatcher constructor.
     *
     * @param array                         $staticRouteMap
     * @param array                         $variableRouteMap
     * @param RegexBasedDispatcherInterface $variableDispatcher
     */
    public function __construct(
        array $staticRouteMap,
        array $variableRouteMap,
        RegexBasedDispatcherInterface $variableDispatcher
    ) {
        $this->staticRouteMap = $staticRouteMap;
        $this->variableRouteMap = $variableRouteMap;
        $this->regexBasedDispatcher = $variableDispatcher;
    }
}
