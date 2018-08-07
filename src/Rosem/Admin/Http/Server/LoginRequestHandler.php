<?php

namespace Rosem\Admin\Http\Server;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Psr\Template\TemplateRendererInterface;

class LoginRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var TemplateRendererInterface
     */
    protected $view;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface  $responseFactory
     * @param TemplateRendererInterface $view
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TemplateRendererInterface $view
    ) {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->view->render(
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
