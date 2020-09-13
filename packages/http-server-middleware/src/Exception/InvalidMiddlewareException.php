<?php

declare(strict_types=1);

/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace Rosem\Component\Http\Server\Exception;

use InvalidArgumentException;

/**
 * Base interface representing a generic exception of an invalid middleware in a route.
 */
class InvalidMiddlewareException extends InvalidArgumentException
{
}
