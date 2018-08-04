<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

function callableBasedMiddleware(ContainerInterface $container, callable $callable): MiddlewareInterface
{
    return new CallableBasedMiddleware($container, $callable);
}

function callableBasedRequestHandler(ContainerInterface $container, callable $callable): RequestHandlerInterface
{
    return new CallableBasedRequestHandler($container, $callable);
}

function deferredFactoryMiddleware(ContainerInterface $container, string $middlewareFactory): MiddlewareInterface
{
    return new DeferredFactoryMiddleware($container, $middlewareFactory);
}

function deferredMiddleware(ContainerInterface $container, string $middleware): MiddlewareInterface
{
    return new DeferredMiddleware($container, $middleware);
}
