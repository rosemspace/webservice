<?php

namespace Rosem\Component\Http\Server;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

class InternalServerErrorRequestHandler implements RequestHandlerInterface
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
     * MainController constructor.
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

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(StatusCode::STATUS_INTERNAL_SERVER_ERROR);
        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->view->render('server::500', $this->config);

            if ($viewString) {
                $body->write($viewString);
            }
        }

        return $response;
    }
}
