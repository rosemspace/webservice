<?php

namespace Rosem\Admin\ServiceProvider;

use Psr\Container\ContainerInterface;
use Rosem\Admin\Controller\AdminController;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\RouteCollector\RouteCollectorInterface;

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
                    "/{$container->get('admin')['uri']}[/{relativePath:.*}]",
                    [AdminController::class, 'index']
                );
            },
        ];
    }
}
