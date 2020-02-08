<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Emitter;

use Psr\Http\Message\ResponseInterface;
use Rosem\Contract\Http\Server\EmitterInterface;

class SapiEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /**
     * Emits a response for a PHP SAPI environment.
     * Emits the status line and headers via the header() function, and the
     * body content via the output buffer.
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    public function emit(ResponseInterface $response): bool
    {
        $this->assertNoPreviousOutput();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);

        return true;
    }

    /**
     * Emit the message body.
     *
     * @param ResponseInterface $response
     */
    private function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }
}
