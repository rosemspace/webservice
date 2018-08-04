<?php

namespace Rosem\Psr\Http\Server;

interface MiddlewareQueueInterface
{
    /**
     * @param string           $middleware
     * @param array            $requestAttributes
     * @param int|float|string $priority
     */
    public function add(string $middleware, array $requestAttributes = [], $priority = 0): void;
}
