<?php

namespace Rosem\Admin\Controller;

use Psr\Http\Message\ResponseInterface;
use Rosem\Kernel\UnionMiddleware;
use TrueStd\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response;

class AdminController implements MiddlewareInterface
{
    use UnionMiddleware;

    public function index() : ResponseInterface {
        $response = new Response;
        $response->getBody()->write('Hello from admin controller');

        return $response;
    }
}
