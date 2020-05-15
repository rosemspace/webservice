<?php

namespace Rosem\Component\App\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\App\Http\Server\{
    HomeRequestHandler
};
use Rosem\Contract\{
    Container\ServiceProviderInterface,
    Route\HttpRouteCollectorInterface,
    Template\TemplateRendererInterface
};

use function dirname;
use function mb_strtolower;

class AppServiceProvider implements ServiceProviderInterface
{
    public const CONFIG_DIRECTORY_ROOT = 'directory.root';

    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     * @throws \InvalidArgumentException
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_DIRECTORY_ROOT => static fn() => null,
            'app.name' => static fn(): string => 'Rosem',
            'app.lang' => static fn(): string => 'en',
            'app.meta.charset' => static fn(): string => 'utf-8',
            'app.meta.titlePrefix' => static fn(): string => 'Rosem | ',
            'app.meta.title' => static fn(): string => 'Welcome',
            'app.meta.titleSuffix' => static fn(): string => '',
            HomeRequestHandler::class => static function (ContainerInterface $container): HomeRequestHandler {
                return new HomeRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class),
                    [
                        'metaTitlePrefix' => $container->get('app.meta.titlePrefix'),
                        'metaTitle' => $container->get('app.meta.title'),
                        'metaTitleSuffix' => $container->get('app.meta.titleSuffix'),
                    ]
                );
            },
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getExtensions(): array
    {
        return [
            TemplateRendererInterface::class => static function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ): void {
                $renderer->addPath(
                    dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR .
                    'templates',
                    'app'
                );
                $renderer->addGlobalData(
                    [
                        'appName' => $container->get('app.name'),
                        'appVersion' => $container->get('app.version'),
                        'lang' => mb_strtolower($container->get('app.lang')),
                        'charset' => mb_strtolower($container->get('app.meta.charset')),
                        'metaTitlePrefix' => $container->get('app.meta.titlePrefix'),
                        'metaTitleSuffix' => $container->get('app.meta.titleSuffix'),
                    ]
                );
            },
            HttpRouteCollectorInterface::class => static function (
                ContainerInterface $container,
                HttpRouteCollectorInterface $routeCollector
            ): void {
                $routeCollector->get('/[home[page]]', HomeRequestHandler::class);
            },
        ];
    }
}
