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

final class Middleware
{
    private function __construct()
    {
    }

    public static function defer(ContainerInterface $container, string $middleware): MiddlewareInterface
    {
        return new class ($container, $middleware) implements RequestHandlerInterface {
            private ContainerInterface $container;

            private string $middleware;

            public RequestHandlerInterface $nextHandler;

            public function __construct(ContainerInterface $container, string $middleware)
            {
                $this->container = $container;
                $this->middleware = $middleware;
            }

            /**
             * {@inheritDoc}
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             * @throws \Psr\Container\NotFoundExceptionInterface
             * @throws \Psr\Container\ContainerExceptionInterface
             * @throws InvalidArgumentException
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $middlewareInstance = $this->container->get($this->middleware);

                if (!$middlewareInstance instanceof MiddlewareInterface) {
                    throw new InvalidArgumentException(
                        "The middleware \"$this->middleware\" should implement \"" .
                        MiddlewareInterface::class . '" interface.'
                    );
                }

                return $middlewareInstance->process($request, $this->nextHandler);
            }
        };
    }

    public static function withMiddleware(
        MiddlewareInterface $firstMiddleware,
        MiddlewareInterface $lastMiddleware
    ): MiddlewareInterface {
        return new class ($firstMiddleware, $lastMiddleware) implements MiddlewareInterface {
            private MiddlewareInterface $middleware;

            private RequestHandlerInterface $requestHandler;

            public function __construct(MiddlewareInterface $firstMiddleware, MiddlewareInterface $lastMiddleware)
            {
                $this->middleware = $firstMiddleware;
                $this->requestHandler = new class ($lastMiddleware) implements RequestHandlerInterface {
                    private MiddlewareInterface $middleware;

                    public RequestHandlerInterface $nextHandler;

                    public function __construct(MiddlewareInterface $middleware)
                    {
                        $this->middleware = $middleware;
                    }

                    public function handle(ServerRequestInterface $request): ResponseInterface
                    {
                        return $this->middleware->process($request, $this->nextHandler);
                    }
                };
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                $this->requestHandler->nextHandler = $requestHandler;

                return $this->middleware->process($request, $this->requestHandler);
            }
        };
    }

    public static function fromCallable(callable $middleware): MiddlewareInterface
    {
        return new class ($middleware) implements MiddlewareInterface {
            private $middleware;

            public function __construct(callable $middleware)
            {
                $this->middleware = $middleware;
            }

            /**
             * @inheritdoc
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return ($this->middleware)($request, $handler);
            }
        };
    }

    public static function group(MiddlewareInterface ...$middleware): MiddlewareInterface
    {
        $aggregateMiddleware = new GroupMiddleware();

        foreach ($middleware as $middlewareInstance) {
            $aggregateMiddleware->addMiddleware($middlewareInstance);
        }

        return $aggregateMiddleware;
    }
}
