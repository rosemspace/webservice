<?php

namespace Psrnext\Http\Server;

use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

interface MiddlewareQueueInterface extends RequestHandlerInterface
{
    public function use(MiddlewareInterface $middleware, float $priority = 0): void;
}
