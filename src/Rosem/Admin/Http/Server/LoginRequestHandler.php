<?php

namespace Rosem\Admin\Http\Server;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Rosem\Psr\Config\ConfigInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Psr\Template\TemplateRendererInterface;
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;

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
     * @var ConfigInterface
     */
    protected $config;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface  $responseFactory
     * @param TemplateRendererInterface $view
     * @param ConfigInterface           $config
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TemplateRendererInterface $view,
        ConfigInterface $config
    ) {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
//        if ($request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)->get('userIdentity')) {
        if ($request->getAttribute(AuthenticationMiddleware::getUserIdentityAttribute())) {
            return $this->responseFactory->createResponse(StatusCodeInterface::STATUS_FOUND)
                ->withHeader('Location', '/admin');
        }

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
