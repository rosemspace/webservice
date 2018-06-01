<?php

namespace Rosem\Route;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareProcessorInterface;
use Psrnext\Route\RouteCollectorInterface;
use Psrnext\Route\RouteDispatcherInterface;
use Rosem\Route\Http\Server\HandleRequestMiddleware;
use Rosem\Route\Http\Server\RouteMiddleware;

class RouteServiceProvider implements ServiceProviderInterface
{
    protected $router;

    public function __construct()
    {
        $this->router = new Router();
    }

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
     * @param ContainerInterface $container
     *
     * @return RouteCollectorInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container): RouteCollectorInterface
    {
//        if ($container->has(RouteDispatcherInterface::class)) {
//            return $container->get(RouteDispatcherInterface::class);
//        }
//
//        return new Router();
        return $this->router;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RouteDispatcherInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createRouteDispatcher(ContainerInterface $container): RouteDispatcherInterface
    {
        $container->get(RouteCollectorInterface::class); // TODO: add it in the constructor of the route dispatcher

//        if ($container->has(RouteCollectorInterface::class)) {
//            return $container->get(RouteCollectorInterface::class);
//        }
//
//        return new Router();

        return $this->router;
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
