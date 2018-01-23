<?php

namespace Rosem\Kernel\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rosem\Kernel\UnionMiddleware;
use TrueStd\Http\Server\MiddlewareInterface;
use TrueStd\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

class MainController implements MiddlewareInterface
{
    use UnionMiddleware;

    public function index(
        ServerRequestInterface $serverRequest,
        RequestHandlerInterface $requestHandler
    ) : ResponseInterface {
        $response = new Response;
        $response->getBody()->write('Hello from main controller');

        return $response;
    }
}
