<?php

namespace Rosem\Atlas\Provider;

use Atlas\Orm\Atlas;
use Psr\Container\ContainerInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Environment\EnvironmentInterface;

class AtlasServiceProvider implements ServiceProviderInterface
{
    public function getFactories() : array
    {
        return [
            Atlas::class => function (ContainerInterface $container) {
                $env = $container->get(EnvironmentInterface::class);

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
