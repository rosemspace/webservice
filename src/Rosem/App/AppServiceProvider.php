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
    Environment\EnvironmentMode,
    Route\RouteCollectorInterface,
    Template\TemplateRendererInterface};

class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     * @throws \InvalidArgumentException
     */
    public function getFactories(): array
    {
        return [
            'app.name' => function () {
                return 'Rosem';
            },
            'app.lang' => function () {
                return 'en';
            },
            'app.meta.charset' => function () {
                return 'utf-8';
            },
            'app.meta.title_prefix' => function () {
                return 'Rosem | ';
            },
            'app.meta.title' => function () {
                return 'Welcome';
            },
            'app.meta.title_suffix' => function () {
                return '';
            },
            'app.environment' => function () {
                //                return EnvironmentMode::PRODUCTION;
                return EnvironmentMode::DEVELOPMENT;
            },
            EnvironmentInterface::class => function (ContainerInterface $container) {
                return new Environment($container->get('app.baseDir'));
            },
            HomeRequestHandler::class => function (ContainerInterface $container) {
                return new HomeRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class),
                    [
                        'metaTitlePrefix' => $container->get('app.meta.title_prefix'),
                        'metaTitle' => $container->get('app.meta.title'),
                        'metaTitleSuffix' => $container->get('app.meta.title_suffix'),
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
                    'appEnvironment' => $container->get('app.environment'),
                    'metaTitlePrefix' => $container->get('app.meta.title_prefix'),
                    'metaTitleSuffix' => $container->get('app.meta.title_suffix'),
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
