<?php

namespace Rosem\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Http\Server\EmitterInterface;

class EmitterProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            EmitterInterface::class => [static::class, 'createEmitter']
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
        $stack->push(new class($sapiStreamEmitter) implements EmitterInterface
        {
            /**
             * @var EmitterInterface
             */
            private $emitter;

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
        });

        return $stack;
    }
}
