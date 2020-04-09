<?php

namespace Rosem\Component\Route;

use Rosem\Contract\Route\RouteDispatcherInterface;

class RouteDispatcher implements RouteDispatcherInterface
{
    use RouteMapTrait;
    use RouteDispatcherTrait;

    /**
     * RouteDispatcher constructor.
     *
     * @param array                            $staticRouteMap
     * @param RegexBasedDataGeneratorInterface $variableRouteMap
     * @param RegexBasedDispatcherInterface    $dispatcher
     */
    public function __construct(
        array $staticRouteMap,
        RegexBasedDataGeneratorInterface $variableRouteMap,
        RegexBasedDispatcherInterface $dispatcher
    ) {
        $this->staticRouteMap = $staticRouteMap;
        $this->variableRouteMap = $variableRouteMap;
        $this->dispatcher = $dispatcher;
    }
}
