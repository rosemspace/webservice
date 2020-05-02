<?php

namespace Rosem\Component\Http\Server\Provider;

use Rosem\Component\Container\AbstractAggregatedServiceProvider;

class KernelServiceProvider extends AbstractAggregatedServiceProvider
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
