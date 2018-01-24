<?php

namespace Rosem\Admin\Controller;

use Psr\Http\Message\ResponseInterface;
use TrueStd\Http\Factory\ResponseFactoryInterface;

class AdminController
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * MainController constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index() : ResponseInterface {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('Hello from admin controller');

        return $response;
    }
}
