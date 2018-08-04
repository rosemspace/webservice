<?php

namespace Rosem\Http\Server;

use LogicException;
use Psr\Http\Server\MiddlewareInterface;
use function in_array;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var array
     */
    protected static $attributeSet = [];

    /**
     * @param string $property
     * @param        $value
     *
     * @throws LogicException
     */
    protected static function setAttribute(string $property, $value): void
    {
        if (in_array($property, static::$attributeSet, true)) {
            throw new LogicException("Middleware attribute \"$property\" can be set only once");
        }

        static::$$property = $value;
        static::$attributeSet[] = $property;
    }
}
