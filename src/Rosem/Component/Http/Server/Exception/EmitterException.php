<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Exception;

use Rosem\Contract\Http\Server\EmitterExceptionInterface;
use RuntimeException;

class EmitterException extends RuntimeException implements EmitterExceptionInterface
{
    public static function dueToAlreadySentHeaders(): self
    {
        return new self('Unable to emit response; headers already sent');
    }

    public static function dueToAlreadySentOutput(): self
    {
        return new self('Output has been emitted previously; cannot emit response');
    }
}
