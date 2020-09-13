<?php

declare(strict_types=1);

namespace Rosem\Component\Hash\Provider;

use Rosem\Component\Hash\ArgonHasher;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Hash\HasherInterface;

class HashServiceProvider implements ServiceProviderInterface
{
    public function getFactories(): array
    {
        return [
            HasherInterface::class => [static::class, 'createHasher'],
        ];
    }

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
