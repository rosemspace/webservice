<?php

namespace Rosem\Component\Authentication\Provider;

use Psr\Container\ContainerInterface;
use Rosem\Component\Authentication\UserFactory;
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;

class UserFactoryProvider implements ServiceProviderInterface
{
    public const CONFIG_RESOLVER_ROLES = 'auth.user.resolver.roles';

    public const CONFIG_RESOLVER_DETAILS = 'auth.user.resolver.details';

    /**
     * {@inheritdoc}
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_RESOLVER_ROLES => static function () {
                return static function (string $username) {
                    return ['admin'];
                };
            },
            static::CONFIG_RESOLVER_DETAILS => static function () {
                return static function (string $username) {
                    return ['username' => $username];
                };
            },
            UserFactoryInterface::class => static function (ContainerInterface $container) {
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
