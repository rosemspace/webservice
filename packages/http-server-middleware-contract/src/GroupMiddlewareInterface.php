<?php

namespace Rosem\Contract\Http\Server;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Interface GroupMiddlewareInterface.
 *
 * @package Rosem\Contract\Http\Server
 */
interface GroupMiddlewareInterface extends MiddlewareInterface
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
