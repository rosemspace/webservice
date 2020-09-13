<?php
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

declare(strict_types=1);

namespace Rosem\Component\Route\Contract;

use Rosem\Contract\Route\BadRouteExceptionInterface;
use Rosem\Contract\Route\TooLongRouteExceptionInterface;

/**
 * Route dispatcher returns data of a route.
 */
interface RouteDispatcherInterface
{
    public const NOT_FOUND = 0;

    public const FOUND = 1;

    public const SCOPE_NOT_ALLOWED = 2;

    /**
     * Generate regular expressions for the all added routes.
     *
     * @throws BadRouteExceptionInterface
     * @throws TooLongRouteExceptionInterface
     */
    public function generate(): void;

    /**
     * Dispatches against the provided scope and route.
     *
     * @return array<int, mixed> The status code, data and variables
     */
    public function dispatch(string $scope, string $route): array;
}
