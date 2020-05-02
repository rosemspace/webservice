<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use Rosem\Component\Route\Map\MarkBasedMap;
use Rosem\Contract\Route\{
    HttpRouteCollectorInterface,
    HttpRouteCollectorTrait
};

/**
 * Class RouteCollector.
 */
class Router extends MarkBasedMap implements HttpRouteCollectorInterface
{
    use HttpRouteCollectorTrait;
//    use HttpAllowedMethodTrait;

    /** TODO: disable / enable some methods */
    public function setAllowedMethods(array $methods)
    {
    }

    public function isMethodAllowed(string $method)
    {
    }
}
