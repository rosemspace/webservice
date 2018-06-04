<?php

namespace Psrnext\Http\Server;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareProcessorInterface extends RequestHandlerInterface, MiddlewareInterface
{
    public function use(string $middlewareClass, float $priority = 0): void;
}
