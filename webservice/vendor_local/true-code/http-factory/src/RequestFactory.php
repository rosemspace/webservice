<?php

namespace TrueCode\Http\Factory;

use Psr\Http\Message\RequestInterface;
use TrueStd\Http\Factory\RequestFactoryInterface;
use Zend\Diactoros\Request;

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest($method, $uri) : RequestInterface
    {
        return new Request($uri, $method);
    }
}
