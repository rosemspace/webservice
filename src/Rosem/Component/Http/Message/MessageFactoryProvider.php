<?php

namespace Rosem\Component\Http\Message;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    ServerRequestFactoryInterface};
use Rosem\Contract\Container\ServiceProviderInterface;
use Zend\Diactoros\{
    ResponseFactory,
    ServerRequestFactory};

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
