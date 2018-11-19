<?php

namespace Rosem\Psr\Http\Server;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareCollectorInterface
{
    /**
     * @param MiddlewareInterface $middleware
     */
    public function add(MiddlewareInterface $middleware): void;

    /**
     * @param string           $middleware
     * @param int|float|string $priority
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function use(string $middleware, $priority = 0): void;
}
