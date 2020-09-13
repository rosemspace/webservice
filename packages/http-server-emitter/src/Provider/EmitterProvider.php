<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Provider;

use Rosem\Component\Http\Server\Emitter\{
    SapiEmitter,
    SapiStreamEmitter
};
use Rosem\Component\Http\Server\EmitterStack;
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
        $stack->push($this->createSapiStreamEmitter());

        return $stack;
    }
}
