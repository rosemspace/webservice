<?php

namespace Rosem\Component\Http\Provider;

use Laminas\Diactoros\{
    ResponseFactory,
    ServerRequest,
    ServerRequestFactory
};
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    ServerRequestInterface
};
use Rosem\Contract\Container\ServiceProviderInterface;

class MessageServiceProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class => [static::class, 'createResponseFactory'],
            ServerRequestInterface::class => [static::class, 'createServerRequest'],
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

    public function createServerRequest(): ServerRequest
    {
        return ServerRequestFactory::fromGlobals();
    }
}
