<?php

namespace Rosem\Kernel\Middleware;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;

class ViewMiddleware implements MiddlewareInterface
{
    /**
     * @var ViewRendererInterface
     */
    protected $viewRenderer;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * ViewMiddleware constructor.
     *
     * @param ViewRendererInterface    $viewRenderer
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(
        ViewRendererInterface $viewRenderer,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->viewRenderer = $viewRenderer;
        $this->responseFactory = $responseFactory;
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
        $viewData = $request->getAttribute('view-data');
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($this->viewRenderer->render($viewData['__template__'], $viewData));

        return $response;
    }
}
