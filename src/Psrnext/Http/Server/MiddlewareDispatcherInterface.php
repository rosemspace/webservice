<?php

namespace Psrnext\Http\Server;

interface MiddlewareDispatcherInterface
{
    public function use(string $middlewareClass, float $priority = 0): void;

    public function dispatch(): void;
}
