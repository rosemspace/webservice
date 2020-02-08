<?php

namespace Rosem\Component\Http\Provider;

use Psr\Http\Message\ResponseInterface;
use Rosem\Component\Http\Server\{
    EmitterStack,
    Emitter\SapiEmitter,
    Emitter\SapiStreamEmitter
};
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\EmitterInterface;

class EmitterProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            EmitterInterface::class => [static::class, 'createEmitter'],
        ];
    }

    /**
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [];
    }

    public function createSapiEmitter(): EmitterInterface
    {
        return new SapiEmitter();
    }

    public function createSapiStreamEmitter(): EmitterInterface
    {
        return new SapiStreamEmitter();
    }

    public function createEmitter(): EmitterInterface
    {
        $stack = new EmitterStack();
        $stack->push($this->createSapiEmitter());
        $sapiStreamEmitter = $this->createSapiStreamEmitter();
        $stack->push(
            new class($sapiStreamEmitter) implements EmitterInterface {
                /**
                 * @var EmitterInterface
                 */
                private EmitterInterface $emitter;

                public function __construct(EmitterInterface $emitter)
                {
                    $this->emitter = $emitter;
                }

                /**
                 * @param ResponseInterface $response
                 *
                 * @return bool
                 */
                public function emit(ResponseInterface $response): bool
                {
                    if (!$response->hasHeader('Content-Disposition')
                        && !$response->hasHeader('Content-Range')
                    ) {
                        return false;
                    }

                    return $this->emitter->emit($response);
                }
            }
        );

        return $stack;
    }
}
