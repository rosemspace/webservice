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

    public static function dueToInvalidVariableRegExp(string $regExp, string $variableName): self
    {
        return new self(sprintf(
            'Regular expression "%s" is not valid for "%s" variable.',
            $regExp,
            $variableName
        ));
    }
}
