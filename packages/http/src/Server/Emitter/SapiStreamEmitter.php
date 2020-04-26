<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Emitter;

use Psr\Http\Message\ResponseInterface;
use Rosem\Contract\Http\Server\EmitterInterface;

use function preg_match;
use function strlen;
use function substr;

class SapiStreamEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /**
     * @var int Maximum output buffering size for each iteration.
     */
    private int $maxBufferLength;

    public function __construct(int $maxBufferLength = 8192)
    {
        $this->maxBufferLength = $maxBufferLength;
    }

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
        if (!$response->hasHeader('Content-Disposition') &&
            !$response->hasHeader('Content-Range')
        ) {
            return false;
        }

        $this->assertNoPreviousOutput();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $range = $this->parseContentRange($response->getHeaderLine('Content-Range'));

        if (null === $range || 'bytes' !== $range[0]) {
            $this->emitBody($response);

            return true;
        }

        $this->emitBodyRange($range, $response);

        return true;
    }

    /**
     * Emit the message body.
     *
     * @param ResponseInterface $response
     */
    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        if (!$body->isReadable()) {
            echo $body;

            return;
        }

        while (!$body->eof()) {
            echo $body->read($this->maxBufferLength);
        }
    }

    /**
     * Emit a range of the message body.
     *
     * @param array             $range
     * @param ResponseInterface $response
     */
    private function emitBodyRange(array $range, ResponseInterface $response): void
    {
        [$unit, $first, $last, $length] = $range;
        $body = $response->getBody();
        $length = $last - $first + 1;

        if ($body->isSeekable()) {
            $body->seek($first);
            $first = 0;
        }

        if (!$body->isReadable()) {
            echo substr($body->getContents(), $first, $length);

            return;
        }

        $remaining = $length;

        while ($remaining >= $this->maxBufferLength && !$body->eof()) {
            $contents = $body->read($this->maxBufferLength);
            $remaining -= strlen($contents);
            echo $contents;
        }

        if ($remaining > 0 && !$body->eof()) {
            echo $body->read($remaining);
        }
    }

    /**
     * Parse content-range header
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
     *
     * @param string $header
     *
     * @return null|array [unit, first, last, length]; returns null if no
     *     content range or an invalid content range is provided
     */
    private function parseContentRange(string $header): ?array
    {
        if (!preg_match(
            '/(?P<unit>[\w]+)\s+(?P<first>\d+)-(?P<last>\d+)\/(?P<length>\d+|\*)/',
            $header,
            $matches
        )) {
            return null;
        }

        return [
            $matches['unit'],
            (int)$matches['first'],
            (int)$matches['last'],
            $matches['length'] === '*' ? '*' : (int)$matches['length'],
        ];
    }
}
