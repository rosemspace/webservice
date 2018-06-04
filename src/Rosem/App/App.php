<?php

namespace Rosem\App;

use Rosem\Http\Server\MiddlewareProcessor;
use Zend\Diactoros\{
    Server, ServerRequestFactory
};

class App extends MiddlewareProcessor
{
    public function boot(): void
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response = $this->handle($request);
        $server = Server::createServerFromRequest(function () {}, $request, $response);
        $server->listen();
    }
}
