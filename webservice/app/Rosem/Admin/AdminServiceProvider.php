<?php

namespace Rosem\Admin;

use Psr\Container\ContainerInterface;
use Rosem\Admin\Controller\AdminController;
use Psrnext\App\AppConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\RouteCollector\RouteCollectorInterface;
use Psrnext\View\ViewInterface;

class AdminServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     */
    public function getExtensions() : array
    {
        return [
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get(
                    "/{$container->get(AppConfigInterface::class)->get('admin.uri')}[/{relativePath:.*}]",
                    [AdminController::class, 'index']
                );
            },
            ViewInterface::class => function (ContainerInterface $container, ViewInterface $view) {
                $view->addDirectoryAlias('Rosem.Admin', __DIR__ . '/View');
            },
        ];
    }
}
