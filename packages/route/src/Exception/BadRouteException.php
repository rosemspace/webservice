<?php

namespace Rosem\Component\Route\Exception;

use LogicException;

class BadRouteException extends LogicException
{
    public static function forDuplicatedRoute(string $route, $httpMethod): self
    {
        return new self(sprintf(
            'Cannot register two routes matching "%s" for method "%s"',
            $route, $httpMethod
        ));
    }
}
