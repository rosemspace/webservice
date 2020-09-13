<?php

declare(strict_types=1);

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
     */
    public function addMiddleware(MiddlewareInterface $middleware): self;
}
