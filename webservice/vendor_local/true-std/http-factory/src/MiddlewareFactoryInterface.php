<?php

namespace TrueStd\Http\Factory;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
    public function createMiddleware($callable) : MiddlewareInterface;
}
