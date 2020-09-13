<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};

final class RequestHandler implements RequestHandlerInterface
{
    private array $middlewareGroup;

    private MiddlewareInterface $middleware;

    private RequestHandlerInterface $requestHandler;

    public function __construct(array $middlewareGroup, RequestHandlerInterface $requestHandler)
    {
        $this->middlewareGroup = $middlewareGroup;
        $this->requestHandler = $requestHandler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($this->middleware)) {
            $this->middleware = Middleware::group(...$this->middlewareGroup);
        }

        return $this->middleware->process($request, $this->requestHandler);
    }

    public static function withMiddleware(
        MiddlewareInterface $middleware,
        RequestHandlerInterface $requestHandler
    ): RequestHandlerInterface {
        return new class ($middleware, $requestHandler) implements RequestHandlerInterface {
            private MiddlewareInterface $middleware;

            private RequestHandlerInterface $nextHandler;

            public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $requestHandler)
            {
                $this->middleware = $middleware;
                $this->nextHandler = $requestHandler;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->nextHandler);
            }
        };
    }

    public static function defer(ContainerInterface $container, string $requestHandlerClass): RequestHandlerInterface
    {
        return new class ($container, $requestHandlerClass) implements RequestHandlerInterface {
            private ContainerInterface $container;

            private string $requestHandlerClass;

            public function __construct(ContainerInterface $container, string $requestHandlerClass)
            {
                $this->container = $container;
                $this->requestHandlerClass = $requestHandlerClass;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->container->get($this->requestHandlerClass)->handle($request);
            }
        };
    }
}
