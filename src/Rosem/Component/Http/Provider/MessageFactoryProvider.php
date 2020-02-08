<?php

namespace Rosem\Component\Http\Provider;

use Laminas\Diactoros\{
    ResponseFactory,
    ServerRequestFactory
};
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ServerRequestFactoryInterface
};
use Rosem\Contract\Container\ServiceProviderInterface;

class MessageFactoryProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class => [static::class, 'createResponseFactory'],
        ];
    }

    /**
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [];
    }

    public function createServerRequestFactory(): ServerRequestFactory
    {
        return new ServerRequestFactory();
    }

    public function createResponseFactory(): ResponseFactory
    {
        return new ResponseFactory();
    }
}
