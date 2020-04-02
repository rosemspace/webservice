<?php

namespace Rosem\Component\Http\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\{
    Message\ResponseFactoryInterface,
    Server\RequestHandlerInterface
};
use Rosem\Component\Http\Server\{
    InternalServerErrorRequestHandler,
    MiddlewareCollector
};
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\{
    MiddlewareCollectorInterface
};
use Rosem\Contract\Template\TemplateRendererInterface;

class MiddlewareProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            MiddlewareCollectorInterface::class => [static::class, 'createMiddlewareCollector'],
            RequestHandlerInterface::class => fn(ContainerInterface $container) => $container->get(
                MiddlewareCollectorInterface::class
            ),
            InternalServerErrorRequestHandler::class => [
                static::class,
                'createInternalServerErrorRequestHandler',
            ],
        ];
    }

    /**
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [
            TemplateRendererInterface::class => static function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $s = DIRECTORY_SEPARATOR;
                $renderer->addPath(
                    __DIR__ . "$s..{$s}Resource{$s}templates",
                    'server'
                );
            },
        ];
    }

    public function createMiddlewareCollector(ContainerInterface $container): MiddlewareCollector
    {
        return new MiddlewareCollector(
            $container,
            $container->get(InternalServerErrorRequestHandler::class)
        );
    }

    public function createInternalServerErrorRequestHandler(
        ContainerInterface $container
    ): RequestHandlerInterface {
        return new InternalServerErrorRequestHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
