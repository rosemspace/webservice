<?php

namespace Psrnext\Http\Server;

use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareQueueInterface extends RequestHandlerInterface
{
    public function use(string $middlewareClass, float $priority = 0): void;
}
