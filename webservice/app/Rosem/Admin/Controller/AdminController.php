<?php

namespace Rosem\Admin\Controller;

use Psr\Http\Message\ResponseInterface;
use Psrnext\App\AppConfigInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;

class AdminController
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
            'Rosem\Admin::templates/admin',
            [
                'metaTitlePrefix' => $this->appConfig->get('admin.meta.titlePrefix'),
                'metaTitle'       => $this->appConfig->get('admin.meta.title'),
                'metaTitleSuffix' => $this->appConfig->get('admin.meta.titleSuffix'),
            ]
        ));

        return $response;
    }
}
