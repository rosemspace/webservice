<?php

namespace Rosem\App;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Rosem\Container\Container;

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    private $container;

    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    private $nextHandler;

    public function __construct(
        Container $container,
        string $middleware
    ) {
        $this->container = $container;
        $this->middleware = $middleware;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (\is_string($this->middleware)) {
            $this->middleware = $this->container->has($this->middleware)
                ? $this->container->get($this->middleware)
                : $this->container->defineNow($this->middleware)->make();
        }

        return $this->middleware->process($request, $this->nextHandler);
    }

    public function &getNextHandlerPointer()
    {
        return $this->nextHandler;
    }
}
