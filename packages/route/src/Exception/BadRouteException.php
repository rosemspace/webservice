<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Exception;

use LogicException;

use function sprintf;

class BadRouteException extends LogicException
{
    public static function forDuplicatedRoute(string $route, $httpMethod): self
    {
        return new self(sprintf(
            'Cannot register two routes matching "%s" for method "%s"',
            $route,
            $httpMethod
        ));
    }

    public static function forEmptyRoute(): self
    {
        return new self('A route cannot be empty.');
    }

    public static function dueToInvalidVariableRegExp(string $regExp, string $variableName): self
    {
        return new self(sprintf(
            'Regular expression "%s" is not valid for "%s" variable.',
            $regExp,
            $variableName
        ));
    }

    public static function dueToWrongOptionalSegmentPosition(): self
    {
        return new self('Optional segments can only occur at the end of a route');
    }

    public static function dueToWrongOptionalSegmentPair(string $openToken, string $closeToken): self
    {
        return new self("Number of opening \"$openToken\" and closing \"$closeToken\" does not match.");
    }

    public static function dueToEmptyOptionalSegment(): self
    {
        return new self("Empty optional segment.");
    }
}
