<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Component\Route\Contract;

/**
 * Route dispatcher returns data of a route.
 */
interface RouteDispatcherInterface
{
    /**
     * Dispatches against the provided scope and route.
     *
     * @param string $scope
     * @param string $route
     *
     * @return array<int, mixed> The status code, data and variables
     */
    public function dispatch(string $scope, string $route): array;
}
