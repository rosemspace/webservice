<?php

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use function function_exists;

if (!function_exists('callableBasedMiddleware')) {
    function callableBasedMiddleware(ContainerInterface $container, callable $callable): MiddlewareInterface
    {
        return new CallableBasedMiddleware($container, $callable);
    }
}

if (!function_exists('callableBasedRequestHandler')) {
    function callableBasedRequestHandler(ContainerInterface $container, callable $callable): RequestHandlerInterface
    {
        return new CallableBasedRequestHandler($container, $callable);
    }
}

if (!function_exists('lazyFactoryMiddleware')) {
    function lazyFactoryMiddleware(ContainerInterface $container, string $middlewareFactory): MiddlewareInterface
    {
        return new LazyFactoryMiddleware($container, $middlewareFactory);
    }
}

if (!function_exists('lazyMiddleware')) {
    function lazyMiddleware(ContainerInterface $container, string $middleware): MiddlewareInterface
    {
        return new LazyMiddleware($container, $middleware);
    }
}
