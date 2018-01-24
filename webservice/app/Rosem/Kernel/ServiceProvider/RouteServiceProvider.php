<?php

namespace Rosem\Kernel\ServiceProvider;

use FastRoute\RouteParser\Std;
use Psr\Container\ContainerInterface;
use Rosem\Kernel\Controller\MainController;
use TrueCode\RouteCollector\{
    RouteDataGenerator, RouteDispatcher
};
use TrueCode\RouteCollector\RouteCollector;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\RouteCollector\{
    RouteCollectorInterface, RouteDispatcherInterface
};

class RouteServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [
            RouteCollectorInterface::class  => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class => [static::class, 'createSimpleRouteDispatcher'],
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     */
    public function getExtensions() : array
    {
        return [
            RouteCollectorInterface::class => function (RouteCollectorInterface $r) {
                $r->get('/{relativePath:.*}', [MainController::class, 'index']);
            },
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container)
    {
        return new RouteCollector(
            new Std,
            new RouteDataGenerator($container->get('kernel')['route']['dataGenerator'])
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createSimpleRouteDispatcher(ContainerInterface $container)
    {
        return new RouteDispatcher(
            $container->get(RouteCollectorInterface::class)->getData(),
            $container->get('kernel')['route']['dispatcher']
        );
    }
}
