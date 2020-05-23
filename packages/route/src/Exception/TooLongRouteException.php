<?php

namespace Rosem\Component\Route\Exception;

use Rosem\Contract\Route\TooLongRouteExceptionInterface;
use RuntimeException;

use function sprintf;

class TooLongRouteException extends RuntimeException implements TooLongRouteExceptionInterface
{
    public static function dueToLongRoute(string $routePattern): self
    {
        return new self(sprintf('Your route "%s" is too long', $routePattern));
    }
}
