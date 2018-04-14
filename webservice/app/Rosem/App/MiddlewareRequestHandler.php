<?php

namespace Rosem\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    private $container;

    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    private $nextHandler;

    public function __construct(
        ContainerInterface $container,
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
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->container->get($this->middleware)->process($request, $this->nextHandler);
    }

    public function &getNextHandlerPointer()
    {
        return $this->nextHandler;
    }
}
