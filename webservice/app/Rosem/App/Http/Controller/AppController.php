<?php

namespace Rosem\App\Http\Controller;

use Psr\Http\Message\ResponseInterface;
use Psrnext\Config\ConfigInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;

class AppController
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

    public function index(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $body = $response->getBody();

        if ($body->isWritable()) {
            $viewString = $this->view->render(
                'index',
                [
                    'metaTitlePrefix' => $this->config->get('app.meta.titlePrefix', ''),
                    'metaTitle'       => $this->config->get(
                        'app.meta.title',
                        $this->config->get('app.name', 'Rosem')
                    ),
                    'metaTitleSuffix' => $this->config->get('app.meta.titleSuffix', ''),
                ]
            );

            if ($viewString) {
                $body->write($viewString);
            }
        }

        return $response;
    }
}
