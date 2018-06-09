<?php

namespace Rosem\App;

use Rosem\Http\Server\MiddlewareQueue;
use Zend\Diactoros\Server;

class App extends MiddlewareQueue
{
    public function boot(): void
    {
        $server = Server::createServer([$this, 'handle'], $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $server->listen();
    }
}
