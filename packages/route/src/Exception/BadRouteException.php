<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Exception;

use LogicException;
use Rosem\Contract\Route\BadRouteExceptionInterface;

use function rtrim;
use function sprintf;

class BadRouteException extends LogicException implements BadRouteExceptionInterface
{
    public static function forDuplicatedRoute(string $route, string $scope): self
    {
        return new self(sprintf('Cannot register two routes matching "%s" for scope "%s"', $route, $scope));
    }

    public static function forShadowedStaticRoute(
        string $staticRoute,
        string $variableRoutePattern,
        string $scope
    ): self {
        return new self(
            sprintf(
                'Static route "%s" is shadowed by previously defined variable route "%s" for scope "%s"',
                $staticRoute,
                $variableRoutePattern,
                $scope
            )
        );
    }

    public static function forEmptyRoute(): self
    {
        return new self('A route cannot be empty.');
    }

    public static function dueToIncompatibilityWithPreviousPattern(
        string $routePattern,
        string $regex = '',
        string $message = ''
    ): self {
        return new self(
            rtrim(
                sprintf(
                    'The route pattern "%s" is incompatible with already added route patterns%s %s',
                    $routePattern,
                    $regex === ''
                        ? '.'
                        : sprintf(' because of the following regular expression "%s".', $regex),
                    $message
                ),
                ' .'
            )
        );
    }

    public static function dueToInvalidVariableRegex(
        string $regex,
        string $variableName = '',
        string $message = ''
    ): self {
        return new self(
            rtrim(
                sprintf(
                    'Regular expression "%s" is not valid%s %s',
                    $regex,
                    ($variableName === '' ? '.' : sprintf(' for "%s" variable.', $variableName)),
                    $message
                ),
                ' .'
            )
        );
    }

    public static function forCapturingGroup(string $regex, string $variableName): self
    {
        return new self(sprintf('Regex "%s" for parameter "%s" contains a capturing group', $regex, $variableName));
    }

    public static function dueToWrongOptionalSegmentPosition(): self
    {
        return new self('Optional segments can only occur at the end of a route');
    }

    public static function dueToWrongOptionalSegmentPair(string $openToken, string $closeToken): self
    {
        return new self("Number of opening \"${openToken}\" and closing \"${closeToken}\" does not match.");
    }

    public static function dueToEmptyOptionalSegment(): self
    {
        return new self('Empty optional segment.');
    }
}
