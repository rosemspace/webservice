<?php

namespace Rosem\Admin;

use Psr\Container\ContainerInterface;
use Rosem\Admin\Controller\AdminController;
use TrueStd\App\AppConfigInterface;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\RouteCollector\RouteCollectorInterface;
use TrueStd\View\ViewInterface;

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
            RouteCollectorInterface::class => function (ContainerInterface $container) {
                $container->get(RouteCollectorInterface::class)->get(
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
