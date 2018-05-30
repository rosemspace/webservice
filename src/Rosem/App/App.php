<?php

namespace Rosem\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psrnext\Http\Server\MiddlewareProcessorInterface;
use Rosem\Http\Server\MiddlewareProcessor;
use Zend\Diactoros\{
    Server, ServerRequestFactory
};

class App extends MiddlewareProcessor implements MiddlewareProcessorInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->defaultHandler->handle($request);
    }

    public function boot(): void
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response = $this->handle($request);
        $server = Server::createServerFromRequest(function () {}, $request, $response);
        $server->listen();
    }
}
