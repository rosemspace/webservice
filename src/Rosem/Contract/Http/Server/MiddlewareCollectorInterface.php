<?php

namespace Rosem\Contract\Http\Server;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareCollectorInterface
{
    /**
     * @param MiddlewareInterface $middleware
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
