<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};
use Rosem\Component\Http\Server\Middleware\HandleErrorMiddleware;
use Rosem\Component\Http\Server\{
    GroupMiddleware,
    InternalServerErrorRequestHandler,
    RequestHandler
};
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\GroupMiddlewareInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

class MiddlewareProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            GroupMiddlewareInterface::class => [static::class, 'createGroupMiddleware'],
            HandleErrorMiddleware::class => [static::class, 'createErrorMiddleware'],
            RequestHandlerInterface::class => [static::class, 'createRequestHandler'],
            InternalServerErrorRequestHandler::class => [static::class, 'createInternalServerErrorRequestHandler'],
        ];
    }

    /**
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [
            GroupMiddlewareInterface::class => static function (
                ContainerInterface $container,
                GroupMiddlewareInterface $middlewareCollector
            ): void {
                if ($container->has(TemplateRendererInterface::class)) {
                    // @TODO make it deferred if an application API based only
                    $middlewareCollector->addMiddleware($container->get(HandleErrorMiddleware::class));
                }
            },
        ];
    }

    public function createGroupMiddleware(): GroupMiddleware
    {
        return new GroupMiddleware();
    }

    public function createErrorMiddleware(ContainerInterface $container): MiddlewareInterface
    {
        return HandleErrorMiddleware::fromContainer($container);
    }

    public function createRequestHandler(ContainerInterface $container): RequestHandlerInterface
    {
        return RequestHandler::withMiddleware(
            $container->get(GroupMiddlewareInterface::class),
            $container->get(InternalServerErrorRequestHandler::class)
        );
    }

    public function createInternalServerErrorRequestHandler(
        ContainerInterface $container
    ): RequestHandlerInterface {
        return new InternalServerErrorRequestHandler($container->get(ResponseFactoryInterface::class));
    }
}
