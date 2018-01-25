<?php

namespace Rosem\Kernel\Controller;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use TrueStd\Http\Factory\ResponseFactoryInterface;

class MainController
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

    public function index(
        ServerRequestInterface $serverRequest,
        RequestHandlerInterface $requestHandler
    ) : ResponseInterface {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('Hello from main controller');

        return $response;
    }
}
