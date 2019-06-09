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
     *         $handler,
     *         ['varName' => 'value', other variables...]
     *     ]
     * @see \Fig\Http\Message\RequestMethodInterface
     * @see \Fig\Http\Message\StatusCodeInterface
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array The handler and variables
     */
    public function dispatch(string $httpMethod, string $uri): array;
}
