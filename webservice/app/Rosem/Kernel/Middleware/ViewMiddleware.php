<?php

namespace Rosem\Kernel\Middleware;

use TrueStd\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrueStd\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response;

class ViewMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
//        $response = $handler->handle($request);
        $response = new Response;
        $userID = $request->getAttribute('id');
        $response->getBody()->write("Hello user with id $userID!");

        return $response;
    }
}
