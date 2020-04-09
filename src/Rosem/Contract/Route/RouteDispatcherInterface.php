<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Contract\Route;

/**
 * Route dispatcher returns handlers of a route.
 */
interface RouteDispatcherInterface
{
    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array The handler and variables
     */
    public function dispatch(string $httpMethod, string $uri): array;
}
