<?php

namespace Rosem\App;

use Exception;
use Psrnext\Http\Server\MiddlewareDispatcherInterface;
use Rosem\Http\Server\MiddlewareDispatcher;
use Zend\Diactoros\{
    Server, ServerRequestFactory
};

class App extends MiddlewareDispatcher implements MiddlewareDispatcherInterface
{
    use ConfigFileTrait;

    /**
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function dispatch(): void
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response = $this->defaultHandler->handle($request);
        $server = Server::createServerFromRequest(function () {}, $request, $response);
        $server->listen();
    }
}
