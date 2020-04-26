<?php

namespace Rosem\Contract\Http\Server;

use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};

interface MiddlewareCollectorInterface extends RequestHandlerInterface
{
    /**
     * @param MiddlewareInterface $middleware
     *
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware): void;

    /**
     * @param string           $middleware
     * @param int|float|string $priority
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function addDeferredMiddleware(string $middleware, $priority = 0): void;
}
