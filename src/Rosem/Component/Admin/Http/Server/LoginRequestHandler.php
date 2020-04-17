<?php

namespace Rosem\Component\Admin\Http\Server;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

/**
 * Class LoginRequestHandler.
 */
class LoginRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * @var TemplateRendererInterface
     */
    protected TemplateRendererInterface $templateRenderer;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface  $responseFactory
     * @param TemplateRendererInterface $templateRenderer
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->responseFactory = $responseFactory;
        $this->templateRenderer = $templateRenderer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->templateRenderer->render(
                'admin::login',
                [
                    'metaTitle' => 'Login',
                    'loginUri' => $request->getUri()->getPath(),
                ]
            );

            if ($viewString) {
                $body->write($viewString);
            }
        }

        return $response;
    }
}
