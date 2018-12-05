<?php

namespace Rosem\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\App\Http\Server\{
    HomeRequestHandler};
use Rosem\Environment\Environment;
use Rosem\Psr\{
    Container\ServiceProviderInterface,
    Environment\EnvironmentInterface,
    Route\RouteCollectorInterface,
    Template\TemplateRendererInterface};

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
            static::CONFIG_DIRECTORY_ROOT => function() {
                return null;
            },
            'app.name' => function () {
                return 'Rosem';
            },
            'app.lang' => function () {
                return 'en';
            },
            'app.meta.charset' => function () {
                return 'utf-8';
            },
            'app.meta.titlePrefix' => function () {
                return 'Rosem | ';
            },
            'app.meta.title' => function () {
                return 'Welcome';
            },
            'app.meta.titleSuffix' => function () {
                return '';
            },
            EnvironmentInterface::class => function (ContainerInterface $container) {
                return new Environment($container->get(static::CONFIG_DIRECTORY_ROOT));
            },
            HomeRequestHandler::class => function (ContainerInterface $container) {
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
            TemplateRendererInterface::class => function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addPath(__DIR__ . '/Resource/templates', 'app');
                $renderer->addGlobalData([
                    'appName' => $container->get('app.name'),
                    'lang' => strtolower($container->get('app.lang')),
                    'charset' => strtolower($container->get('app.meta.charset')),
                    'appMode' => $container->get(EnvironmentInterface::class)->getAppMode(),
                    'metaTitlePrefix' => $container->get('app.meta.titlePrefix'),
                    'metaTitleSuffix' => $container->get('app.meta.titleSuffix'),
                ]);
            },
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get('/{appRelativePath.*}', HomeRequestHandler::class);
            },
        ];
    }
}
