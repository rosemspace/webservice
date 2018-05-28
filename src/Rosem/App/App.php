<?php

namespace Rosem\App;

use Exception;
use Psrnext\App\AppInterface;
use Rosem\Http\Server\MiddlewareDispatcher;
use Zend\Diactoros\{
    Server, ServerRequestFactory
};

class App extends MiddlewareDispatcher implements AppInterface
{
    use ConfigFileTrait;

    /**
     * @param array $config
     *
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function boot(array $config): void
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response = $this->defaultHandler->handle($request);
        $server = new Server(function () {
        }, $request, $response);
        $server->listen();
    }

    public function get($id)
    {
        // TODO: Implement get() method.
    }
    public function has($id)
    {
        // TODO: Implement has() method.
    }
}
