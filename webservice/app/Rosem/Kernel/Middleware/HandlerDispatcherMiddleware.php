<?php

namespace Rosem\Kernel\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Rosem\Container\Container;

class HandlerDispatcherMiddleware implements MiddlewareInterface
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //TODO: handler from callable, etc
        return $this->container->call($request->getAttribute('request-handler'), [], [$request, $handler]);
    }
}
