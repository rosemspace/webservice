<?php

namespace Rosem\Admin\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Psrnext\Config\ConfigInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;

class LoginRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var ViewRendererInterface
     */
    protected $view;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param ViewRendererInterface    $view
     * @param ConfigInterface       $config
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ViewRendererInterface $view,
        ConfigInterface $config
    ) {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)->get('userIdentity')) {
            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/admin');
        }

        $response = $this->responseFactory->createResponse();
        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->view->render(
                'admin::login',
                [
                    'metaTitlePrefix' => $this->config->get('app.meta.title_prefix', ''),
                    'metaTitle'       => 'Login',
                    'metaTitleSuffix' => $this->config->get('app.meta.title_suffix', ''),
                ]
            );

            if ($viewString) {
                $body->write($viewString);
            }
        }

        return $response;
    }
}
