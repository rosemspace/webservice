<?php

declare(strict_types=1);

namespace Rosem\Component\Route\Provider;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\Route\Middleware\{
    HandleRequestMiddleware,
    RouteMiddleware
};
use Rosem\Component\Route\{
    RouteParser,
    Router
};
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\GroupMiddlewareInterface;
use Rosem\Contract\Route\HttpRouteCollectorInterface;

class RouteServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     * @throws InvalidArgumentException
     */
    public function getFactories(): array
    {
        return [
            HttpRouteCollectorInterface::class => [static::class, 'createHttpServerRouteCollector'],
            RouteMiddleware::class => [static::class, 'createRouteMiddleware'],
            HandleRequestMiddleware::class => [static::class, 'createHandleRequestMiddleware'],
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     * @throws ContainerExceptionInterface
     */
    public function getExtensions(): array
    {
        return [
            GroupMiddlewareInterface::class => static function (
                ContainerInterface $container,
                GroupMiddlewareInterface $middlewareCollector
            ): void {
                $middlewareCollector->addMiddleware($container->get(RouteMiddleware::class));
                $middlewareCollector->addMiddleware($container->get(HandleRequestMiddleware::class));
            },
        ];
    }

    /**
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     */
    public function createHttpServerRouteCollector(ContainerInterface $container): HttpRouteCollectorInterface
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
