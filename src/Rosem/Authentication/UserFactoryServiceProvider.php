<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Container\ServiceProviderInterface;

class UserFactoryServiceProvider implements ServiceProviderInterface
{
    public const CONFIG_RESOLVER_ROLES = 'auth.user.resolver.roles';

    public const CONFIG_RESOLVER_DETAILS = 'auth.user.resolver.details';

    /**
     * {@inheritdoc}
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_RESOLVER_ROLES => function () {
                return function (string $username) {
                    return ['admin'];
                };
            },
            static::CONFIG_RESOLVER_DETAILS => function () {
                return function (string $username) {
                    return ['username' => $username];
                };
            },
            UserFactoryInterface::class => function (ContainerInterface $container) {
                return new UserFactory(
                    $container->get(static::CONFIG_RESOLVER_ROLES),
                    $container->get(static::CONFIG_RESOLVER_DETAILS)
                );
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(): array
    {
        return [];
    }
}
