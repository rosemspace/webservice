<?php

namespace Rosem\App;

use Rosem\Http\Server\MiddlewareDispatcher;
use Zend\Diactoros\Server;

class App extends MiddlewareDispatcher
{
    public function boot(): void
    {
        $server = Server::createServer([$this, 'handle'], $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $server->listen();
    }
}
