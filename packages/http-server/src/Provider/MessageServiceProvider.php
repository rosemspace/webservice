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
use Psr\Container\ContainerInterface;
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
use Rosem\Contract\Template\TemplateRendererInterface;

use function dirname;

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
        return [
            TemplateRendererInterface::class => static function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addPath(
                    dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR .
                    'templates',
                    'error'
                );
            },
        ];
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
