<?php

namespace Rosem\Component\Route\Middleware;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};
use Rosem\Contract\Template\TemplateRendererInterface;

class ClientErrorMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * @var TemplateRendererInterface
     */
    protected TemplateRendererInterface $view;

    /**
     * @var array
     */
    protected array $config;

    /**
     * ClientErrorMiddleware constructor.
     *
     * @param ResponseFactoryInterface  $responseFactory
     * @param TemplateRendererInterface $view
     * @param array                     $config
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TemplateRendererInterface $view,
        array $config = []
    ) {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        switch ($response->getStatusCode()) {
            case StatusCode::STATUS_NOT_FOUND:
                $this->attachHtmlToResponse($response, StatusCode::STATUS_NOT_FOUND);

                break;
            case StatusCode::STATUS_METHOD_NOT_ALLOWED:
                $this->attachHtmlToResponse($response, StatusCode::STATUS_METHOD_NOT_ALLOWED);

                break;
        }

        return $response;
    }

    public function attachHtmlToResponse(ResponseInterface $response, int $statusCode): void
    {
        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->view->render('route::' . $statusCode, $this->config);

            if ($viewString) {
                $body->write($viewString);
            }
        }
    }
}
