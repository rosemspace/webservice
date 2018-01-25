<?php

namespace Rosem\Kernel;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    private $middleware;
    private $nextHandler;

    public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $nextHandler)
    {
        $this->middleware = $middleware;
        $this->nextHandler = $nextHandler;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return $this->middleware->process($request, $this->nextHandler);
    }
}
