<?php

namespace TrueStd\Http\Factory;

use TrueStd\Http\Server\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
    public function createMiddleware($callable) : MiddlewareInterface;
}
