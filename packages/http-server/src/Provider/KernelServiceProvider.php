<?php

namespace Rosem\Component\Http\Server\Provider;

use Rosem\Component\Container\AggregateServiceProviderTrait;
use Rosem\Contract\Container\ServiceProviderInterface;

class KernelServiceProvider implements ServiceProviderInterface
{
    use AggregateServiceProviderTrait;

    public function __construct()
    {
        $this->addServiceProviders(
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
