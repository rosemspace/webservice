<?php

namespace Rosem\Component\Hash\Provider;

use Rosem\Component\Hash\ArgonHasher;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Hash\HasherInterface;

class HashServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFactories(): array
    {
        return [
            HasherInterface::class => [static::class, 'createHasher']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExtensions(): array
    {
        return [];
    }

    public function createHasher(): ArgonHasher
    {
        //todo params from config
        return new ArgonHasher();
    }
}
