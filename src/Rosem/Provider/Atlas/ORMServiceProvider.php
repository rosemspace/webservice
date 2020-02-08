<?php

namespace Rosem\Provider\Atlas;

use Atlas\Orm\Atlas;
use Psr\Container\ContainerInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Env\EnvInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public function getFactories() : array
    {
        return [
            Atlas::class => function (ContainerInterface $container) {
                $env = $container->get(EnvInterface::class);

                $atlas = Atlas::new(
                    $env->get('DATABASE_DRIVER') .
                    ':host=' . $env->get('DATABASE_HOST') .
                    ';dbname=' . $env->get('DATABASE_NAME'),
                    $env->get('DATABASE_USERNAME'),
                    $env->get('DATABASE_PASSWORD')
                );

                return $atlas;
            },
        ];
    }

    public function getExtensions() : array
    {
        return [];
    }
}
