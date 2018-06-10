<?php

namespace Rosem\Route;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;
use Psrnext\Route\RouteCollectorInterface;
use Psrnext\Route\RouteDispatcherInterface;
use Rosem\Route\Dispatcher\MarkBasedDispatcher;
use Rosem\Route\Http\Server\{
    HandleRequestMiddleware, RouteMiddleware
};

class RouteServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     * @return callable[]
     * @throws \InvalidArgumentException
     */
    public function getFactories(): array
    {
        return [
            RouteCollectorInterface::class  => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class => [static::class, 'createRouteDispatcher'],
            RouteMiddleware::class          => [static::class, 'createRouteMiddleware'],
            HandleRequestMiddleware::class  => [static::class, 'createHandleRequestMiddleware'],
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
            MiddlewareQueueInterface::class => function (
                ContainerInterface $container,
                MiddlewareQueueInterface $middlewareDispatcher
            ) {
                $middlewareDispatcher->use($container->get(RouteMiddleware::class));
                $middlewareDispatcher->use($container->get(HandleRequestMiddleware::class));
            },
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RouteCollectorInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container): RouteCollectorInterface
    {
        return new RouteCollector();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RouteDispatcherInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createRouteDispatcher(ContainerInterface $container): RouteDispatcherInterface
    {
        return new RouteDispatcher($container->get(RouteCollectorInterface::class), new MarkBasedDispatcher());
    }

    public function createRouteMiddleware(ContainerInterface $container) {
        return new RouteMiddleware(
            $container->get(RouteDispatcherInterface::class),
            $container->get(ResponseFactoryInterface::class)
        );
    }

    public function createHandleRequestMiddleware(ContainerInterface $container) {
        return new HandleRequestMiddleware($container);
    }
}
