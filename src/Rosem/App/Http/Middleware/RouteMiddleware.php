<?php

namespace Rosem\App\Http\Middleware;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Router\RouteDispatcherInterface;

class RouteMiddleware implements MiddlewareInterface
{
    protected $router;

    protected $responseFactory;

    /**
     * @var string Attribute name for handler reference
     */
    protected $attribute = 'request-handler';

    public function __construct(
        RouteDispatcherInterface $router,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->router = $router;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Set the attribute name to store handler reference.
     *
     * @param string $attribute
     *
     * @return RouteMiddleware
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $nextHandler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $nextHandler): ResponseInterface
    {
        $route = $this->router->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($route[0] === RouteDispatcherInterface::ROUTE_NOT_FOUND) {
            return $this->createNotFoundResponse();
        }

        if ($route[0] === RouteDispatcherInterface::METHOD_NOT_ALLOWED) {
            return $this->createMethodNotAllowedResponse();
        }

        foreach ($route[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $request = $this->setHandler($request, $route[1]);

        return $nextHandler->handle($request);
    }

    /**
     * Set the handler reference on the request.
     *
     * @param ServerRequestInterface $request
     * @param mixed $handler
     *
     * @return ServerRequestInterface
     */
    protected function setHandler(ServerRequestInterface $request, $handler): ServerRequestInterface
    {
        return $request->withAttribute($this->attribute, $handler);
    }

    public function createNotFoundResponse(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(404);
        $response->getBody()->write('Not found :(');

        return $response;
    }

    public function createMethodNotAllowedResponse(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(405);
        $response->getBody()->write('Method not allowed :(');

        return $response;
    }
}
