<?php

namespace Rosem\Psr\Http\Server;

interface MiddlewareQueueInterface
{
//    public function add(): void;

    /**
     * @param string           $middleware
     * @param array            $requestAttributes
     * @param int|float|string $priority
     */
    public function use(string $middleware, array $requestAttributes = [], $priority = 0): void;
}
