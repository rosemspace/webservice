<?php

namespace Rosem\Route;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareProcessorInterface;
use Psrnext\Router\RouteCollectorInterface;
use Psrnext\Router\RouteDispatcherInterface;
use Rosem\Route\Http\Server\HandleRequestMiddleware;
use Rosem\Route\Http\Server\RouteMiddleware;

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
            MiddlewareProcessorInterface::class => function (
                ContainerInterface $container,
                MiddlewareProcessorInterface $middlewareDispatcher
            ) {
                $middlewareDispatcher->use(RouteMiddleware::class);
                $middlewareDispatcher->use(HandleRequestMiddleware::class);
            },
        ];
    }

    /**
     * @return \Rosem\Router\RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector()
    {
        return new \Rosem\Router\RouteCollector(
            new \FastRoute\RouteParser\Std,
            new \FastRoute\DataGenerator\GroupCountBased
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteDispatcher(ContainerInterface $container)
    {
        return new \Rosem\Router\RouteDispatcher(
            new \FastRoute\Dispatcher\GroupCountBased($container->get(RouteCollectorInterface::class)->getData())
        );
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
