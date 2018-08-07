<?php

namespace Rosem\Psr\Http\Server;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareDispatcherInterface
{
    /**
     * @param MiddlewareInterface $middleware
     * @param int|float|string    $priority
     */
    public function add(MiddlewareInterface $middleware, $priority = 0): void;

    /**
     * @param string           $middleware
     * @param int|float|string $priority
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function use(string $middleware, $priority = 0): void;
}
