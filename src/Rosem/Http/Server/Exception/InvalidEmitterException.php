<?php

declare(strict_types=1);

namespace Rosem\Http\Server\Exception;

use InvalidArgumentException;
use Rosem\Http\Server\EmitterStack;
use Rosem\Psr\Http\Server\{
    InvalidEmitterExceptionInterface,
    EmitterInterface};
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

class InvalidEmitterException extends InvalidArgumentException implements InvalidEmitterExceptionInterface
{
    /**
     * @var mixed $emitter Invalid emitter type
     * @return InvalidEmitterException
     */
    public static function forEmitter($emitter): self
    {
        return new self(sprintf(
            '%s can only compose %s implementations; received %s',
            EmitterStack::class,
            EmitterInterface::class,
            is_object($emitter) ? get_class($emitter) : gettype($emitter)
        ));
    }
}
