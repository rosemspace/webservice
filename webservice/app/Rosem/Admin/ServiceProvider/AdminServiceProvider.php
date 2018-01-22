<?php

namespace Rosem\Admin\ServiceProvider;

use FastRoute\RouteCollector;
use Rosem\Admin\Controller\AdminController;
use TrueStd\Container\ServiceProviderInterface;

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
            RouteCollector::class => function (RouteCollector $r) {
                $r->get( '/admin[/{admin:.*}]', [AdminController::class, 'index']);
            },
        ];
    }
}
