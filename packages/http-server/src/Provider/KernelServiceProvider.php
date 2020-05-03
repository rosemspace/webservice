<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server\Provider;

use Rosem\Component\Container\AggregateProvider;

/**
 * Class KernelServiceProvider.
 */
class KernelServiceProvider extends AggregateProvider
{
    /**
     * KernelServiceProvider constructor.
     */
    public function __construct()
    {
        // Need to create server responses
        $this->addProvider(new MessageServiceProvider());
        // Need to emit a response to a client
        $this->addProvider(new EmitterProvider());
        // Need to allow middleware usage
        $this->addProvider(new MiddlewareProvider());
    }
}
