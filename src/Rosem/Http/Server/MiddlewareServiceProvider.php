<?php

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Http\Server\{
    EmitterInterface,
    MiddlewareCollectorInterface,
    MiddlewareRunnerInterface};
use Rosem\Psr\Template\TemplateRendererInterface;

class MiddlewareServiceProvider implements ServiceProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            MiddlewareCollectorInterface::class => [static::class, 'createMiddlewareCollector'],
            MiddlewareRunnerInterface::class => [static::class, 'createMiddlewareRunner'],
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
            TemplateRendererInterface::class => function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addPath(__DIR__ . '/Resource/templates', 'server');
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

    public function createMiddlewareRunner(ContainerInterface $container): MiddlewareRunner
    {
        return new MiddlewareRunner(
            $container->get(MiddlewareCollectorInterface::class),
            $container->get(EmitterInterface::class)
        );
    }

    public function createInternalServerErrorRequestHandler(
        ContainerInterface $container
    ): RequestHandlerInterface {
        return new InternalServerErrorRequestHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(TemplateRendererInterface::class),
            [
                'metaTitlePrefix' => $container->get('app.meta.title_prefix'),
                'metaTitle' => $container->get('app.meta.title'),
                'metaTitleSuffix' => $container->get('app.meta.title_suffix'),
            ]
        );
    }
}
