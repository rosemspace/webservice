<?php

namespace Rosem\Component\Route\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\Route\{
    RouteCollector,
    Compiler,
    RouteDispatcher,
    Parser
};
use Rosem\Component\Route\DataGenerator\MarkBasedDataGenerator;
use Rosem\Component\Route\Dispatcher\MarkBasedDispatcher;
use Rosem\Component\Route\Middleware\{
    HandleRequestMiddleware,
    RouteMiddleware
};
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;
use Rosem\Contract\Route\{
    RouteCollectorInterface,
    RouteDispatcherInterface
};

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
            RouteCollectorInterface::class => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class => [static::class, 'createRouteDispatcher'],
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
            MiddlewareCollectorInterface::class => function (
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
     * @return RouteCollectorInterface
     * @throws \InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container): RouteCollectorInterface
    {
        return new RouteCollector(new Compiler(new Parser()), new MarkBasedDataGenerator());
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RouteDispatcherInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function createRouteDispatcher(ContainerInterface $container): RouteDispatcherInterface
    {
        /** @var RouteCollector $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        return new RouteDispatcher(
            $collector->getStaticRouteMap(),
            $collector->getVariableRouteMap(),
            new MarkBasedDispatcher()
        );
    }

    public function createRouteMiddleware(ContainerInterface $container): RouteMiddleware
    {
        return new RouteMiddleware(
            $container->get(RouteDispatcherInterface::class),
            $container->get(ResponseFactoryInterface::class)
        );
    }

    public function createHandleRequestMiddleware(ContainerInterface $container): HandleRequestMiddleware
    {
        return new HandleRequestMiddleware($container);
    }
}
