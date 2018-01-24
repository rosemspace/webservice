<?php

namespace TrueCode\Http\Factory;

use Psr\Http\Message\ResponseInterface;
use TrueStd\Http\Factory\ResponseFactoryInterface;
use Zend\Diactoros\Response;

class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse($code = 200) : ResponseInterface
    {
        return (new Response())
            ->withStatus($code);
    }
}
