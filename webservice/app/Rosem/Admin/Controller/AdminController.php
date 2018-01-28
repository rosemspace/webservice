<?php

namespace Rosem\Admin\Controller;

use Psr\Http\Message\ResponseInterface;
use TrueStd\App\AppConfigInterface;
use TrueStd\Http\Factory\ResponseFactoryInterface;
use TrueStd\View\ViewInterface;

class AdminController
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var ViewInterface
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
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ViewInterface $view,
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
                'appName'         => $this->appConfig->get('app.name'),
                'metaTitlePrefix' => $this->appConfig->get('admin.meta.titlePrefix'),
                'metaTitle'       => $this->appConfig->get('admin.meta.title'),
                'metaTitleSuffix' => $this->appConfig->get('admin.meta.titleSuffix'),
            ]
        ));

        return $response;
    }
}
