<?php

namespace Rosem\Contract\Http\Server;

use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};

/**
 * Interface MiddlewareCollectorInterface.
 *
 * @package Rosem\Contract\Http\Server
 */
interface MiddlewareCollectorInterface extends RequestHandlerInterface
{
    /**
     * Add middleware to the collection.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): self;
}
