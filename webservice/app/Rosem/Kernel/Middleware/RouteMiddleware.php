<?php

namespace Rosem\Kernel\Middleware;

use Http\Factory\Diactoros\ResponseFactory;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrueStd\Http\Server\MiddlewareInterface;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        try {
            $response = (new ResponseFactory)->createResponse(200);
            $response->getBody()->write('Hello from router!');
        } catch (\Exception $e) {
            $response = $handler->handle($request);
        }

        return $response;
    }
}
