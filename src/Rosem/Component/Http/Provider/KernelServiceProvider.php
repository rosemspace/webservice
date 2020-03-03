<?php

namespace Rosem\Component\Http\Provider;

use Rosem\Component\Container\AbstractAggregateServiceProvider;

class KernelServiceProvider extends AbstractAggregateServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                // Need to create server responses
                MessageServiceProvider::class,
                // Need to emit a response to a client
                EmitterProvider::class,
                // Need to allow middleware usage
                MiddlewareProvider::class,
            ]
        );
    }
}
