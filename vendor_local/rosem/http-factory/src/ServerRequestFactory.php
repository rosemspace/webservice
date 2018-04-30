<?php

namespace Rosem\Http\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Psrnext\Http\Factory\ServerRequestFactoryInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory as DiactorosServerRequestFactory;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest($method, $uri) : ServerRequestInterface
    {
        $serverParams = [];
        $uploadedFiles = [];

        return new ServerRequest(
            $serverParams,
            $uploadedFiles,
            $uri,
            $method
        );
    }

    public function createServerRequestFromArray(array $server) : ServerRequestInterface
    {
        $normalizedServer = DiactorosServerRequestFactory::normalizeServer($server);
        $headers = DiactorosServerRequestFactory::marshalHeaders($server);

        $request = new ServerRequest(
            $normalizedServer,
            [],
            DiactorosServerRequestFactory::marshalUriFromServer($normalizedServer, $headers),
            DiactorosServerRequestFactory::get('REQUEST_METHOD', $server, 'GET')
        );

        return $request;
    }
}
