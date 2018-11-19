<?php

declare(strict_types=1);

namespace Rosem\Http\Server\Exception;

use Rosem\Psr\Http\Server\EmitterExceptionInterface;
use RuntimeException;

class EmitterException extends RuntimeException implements EmitterExceptionInterface
{
    public static function forHeadersSent(): self
    {
        return new self('Unable to emit response; headers already sent');
    }

    public static function forOutputSent(): self
    {
        return new self('Output has been emitted previously; cannot emit response');
    }
}
