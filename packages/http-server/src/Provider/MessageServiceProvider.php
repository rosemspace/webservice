<?php

namespace Rosem\Component\Http\Server\Provider;

use Laminas\Diactoros\{
    RequestFactory,
    ResponseFactory,
    ServerRequest,
    ServerRequestFactory,
    StreamFactory,
    UploadedFileFactory,
    UriFactory
};
use Psr\Http\Message\{
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    ServerRequestInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
};
use Rosem\Contract\Container\ServiceProviderInterface;

class MessageServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFactories(): array
    {
        return [
            RequestFactoryInterface::class => [static::class, 'createRequestFactoryInterface'],
            ResponseFactoryInterface::class => [static::class, 'createResponseFactory'],
            StreamFactoryInterface::class => [static::class, 'createStreamFactory'],
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ServerRequestInterface::class => [static::class, 'createServerRequest'],
            UploadedFileFactoryInterface::class => [static::class, 'createUploadedFileFactory'],
            UriFactoryInterface::class => [static::class, 'createUriFactory'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExtensions(): array
    {
        return [];
    }

    public function createRequestFactoryInterface(): RequestFactoryInterface
    {
        return new RequestFactory();
    }

    public function createResponseFactory(): ResponseFactory
    {
        return new ResponseFactory();
    }

    public function createStreamFactory(): StreamFactoryInterface
    {
        return new StreamFactory();
    }

    public function createServerRequestFactory(): ServerRequestFactory
    {
        return new ServerRequestFactory();
    }

    public function createServerRequest(): ServerRequest
    {
        return ServerRequestFactory::fromGlobals();
    }

    public function createUploadedFileFactory(): UploadedFileFactoryInterface
    {
        return new UploadedFileFactory();
    }

    public function createUriFactory(): UriFactoryInterface
    {
        return new UriFactory();
    }
}
