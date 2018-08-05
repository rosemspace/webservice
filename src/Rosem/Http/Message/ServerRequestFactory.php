<?php

namespace Rosem\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory as DiactorosServerRequestFactory;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(string $method, $uri, array $serverParams = []) : ServerRequestInterface
    {
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
