<?php

namespace Rosem\BackOffice\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;
use Rosem\BackOffice\Http\Controller\BackOfficeController;
use Psrnext\App\AppConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Router\RouteCollectorInterface;

class BackOfficeServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            BackOfficeController::class => function (ContainerInterface $container) {
                return new BackOfficeController(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(ViewRendererInterface::class),
                    $container->get(AppConfigInterface::class)
                );
            },
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get(
                    "/{$container->get(AppConfigInterface::class)->get('backOffice.uri')}[/{relativePath:.*}]",
                    [BackOfficeController::class, 'index']
                );
            },
        ];
    }
}
