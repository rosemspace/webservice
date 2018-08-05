<?php

namespace Rosem\Http\Message;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;
use Zend\Diactoros\Uri;

class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = '') : UriInterface
    {
        return new Uri($uri);
    }
}
