<?php

namespace Rosem\Component\Route\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\Route\{
    RouteParser,
    Router
};
use Rosem\Component\Route\Middleware\{
    HandleRequestMiddleware,
    RouteMiddleware
};
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;
use Rosem\Contract\Route\HttpRouteCollectorInterface;

class RouteServiceProvider implements ServiceProviderInterface
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
            HttpRouteCollectorInterface::class => [static::class, 'createHttpRouteCollector'],
            RouteMiddleware::class => [static::class, 'createRouteMiddleware'],
            HandleRequestMiddleware::class => [static::class, 'createHandleRequestMiddleware'],
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
            MiddlewareCollectorInterface::class => static function (
                ContainerInterface $container,
                MiddlewareCollectorInterface $middlewareCollector
            ) {
                $middlewareCollector->addDeferredMiddleware(RouteMiddleware::class);
                $middlewareCollector->addDeferredMiddleware(HandleRequestMiddleware::class);
            },
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return HttpRouteCollectorInterface
     * @throws \InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createHttpRouteCollector(ContainerInterface $container): HttpRouteCollectorInterface
    {
        return new Router(new RouteParser());
    }

    public function createRouteMiddleware(ContainerInterface $container): RouteMiddleware
    {
        return new RouteMiddleware(
            $container->get(HttpRouteCollectorInterface::class),
            $container->get(ResponseFactoryInterface::class)
        );
    }

    public function createHandleRequestMiddleware(ContainerInterface $container): HandleRequestMiddleware
    {
        return new HandleRequestMiddleware($container);
    }
}
