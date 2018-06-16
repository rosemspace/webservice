<?php

namespace Psrnext\Http\Server;

use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

interface MiddlewareQueueInterface extends RequestHandlerInterface
{
    /**
     * @param MiddlewareInterface $middleware
     * @param int|float|string    $priority
     */
    public function use(MiddlewareInterface $middleware, $priority = 0): void;
}
