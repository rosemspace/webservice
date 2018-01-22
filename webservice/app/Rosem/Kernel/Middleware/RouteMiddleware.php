<?php

namespace Rosem\Kernel\Middleware;

use TrueStd\Http\Factory\ResponseFactoryInterface;
use TrueStd\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrueStd\Http\Server\MiddlewareInterface;
use FastRoute\Dispatcher;

class RouteMiddleware implements MiddlewareInterface
{
    protected $router;

    protected $responseFactory;

    public function __construct(Dispatcher $router, ResponseFactoryInterface $responseFactory)
    {
        $this->router = $router;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $route = $this->router->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($route[0] === Dispatcher::NOT_FOUND) {
            return $this->createNotFoundResponse();
        }

        if ($route[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->createMethodNotAllowedResponse();
        }

        foreach ($route[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $routeHandler = $route[1];

        return $handler->handle($request);
    }

    public function createNotFoundResponse() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse(404);

        if ($response->getBody()->isWritable()) {
            $response->getBody()->write('Not found :(');
        }

        return $response;
    }

    public function createMethodNotAllowedResponse() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse(405);

        if ($response->getBody()->isWritable()) {
            $response->getBody()->write('Method not allowed :(');
        }

        return $response;
    }
}
