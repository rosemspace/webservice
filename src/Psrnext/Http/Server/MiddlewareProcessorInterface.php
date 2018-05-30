<?php

namespace Psrnext\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareProcessorInterface extends RequestHandlerInterface
{
    public function use(string $middlewareClass, float $priority = 0): void;

    public function handle(ServerRequestInterface $request): ResponseInterface;
}
