<?php

namespace Rosem\Kernel\Controller;

use Psr\Http\Message\ResponseInterface;
use Psrnext\App\AppConfigInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;

class MainController
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
     * @var AppConfigInterface
     */
    protected $appConfig;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param ViewRendererInterface    $view
     * @param AppConfigInterface       $appConfig
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ViewRendererInterface $view,
        AppConfigInterface $appConfig
    ) {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->appConfig = $appConfig;
    }

    public function index() : ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($this->view->render(
            'Rosem\Kernel::templates/main',
            [
                'metaTitle' => $this->appConfig->get(
                    'app.meta.title',
                    $this->appConfig->get('app.name', 'Rosem')
                ),
            ]
        ));

        return $response;
    }
}
