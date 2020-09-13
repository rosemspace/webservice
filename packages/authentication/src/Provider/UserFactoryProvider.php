<?php

declare(strict_types=1);

namespace Rosem\Component\Authentication\Provider;

use Psr\Container\ContainerInterface;
use Rosem\Component\Authentication\UserFactory;
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;

class UserFactoryProvider implements ServiceProviderInterface
{
    public const CONFIG_RESOLVER_ROLES = 'auth.user.resolver.roles';

    public const CONFIG_RESOLVER_DETAILS = 'auth.user.resolver.details';

    public function getFactories(): array
    {
        return [
            static::CONFIG_RESOLVER_ROLES => static function (): callable {
                return static function (string $username) {
                    return ['admin'];
                };
            },
            static::CONFIG_RESOLVER_DETAILS => static function (): callable {
                return static function (string $username) {
                    return ['username' => $username];
                };
            },
            UserFactoryInterface::class => static fn (ContainerInterface $container): UserFactory => new UserFactory(
                $container->get(static::CONFIG_RESOLVER_ROLES),
                $container->get(static::CONFIG_RESOLVER_DETAILS)
            ),
        ];
    }

    public function getExtensions(): array
    {
        return [];
    }
}
