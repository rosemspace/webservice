<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Authentication\Http\Server\{
    BasicAuthenticationMiddleware, DigestAuthenticationMiddleware
};
use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Http\Server\MiddlewareDispatcherInterface;

class HttpAuthenticationServiceProvider implements ServiceProviderInterface
{
    public const CONFIG_TYPE = 'auth.http.type';

    public const CONFIG_USER_RESOLVER_PASSWORD = 'auth.http.user.resolver.password';

    /**
     * {@inheritdoc}
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_TYPE => function () {
                return 'digest';
            },
            static::CONFIG_USER_RESOLVER_PASSWORD => function () {
                return function (string $username): ?string {
                    return ['admin' => 'admin'][$username] ?? null;
                };
            },
            BasicAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new BasicAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(UserFactoryInterface::class),
                    $container->get(static::CONFIG_USER_RESOLVER_PASSWORD)
                );
            },
            DigestAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new DigestAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(UserFactoryInterface::class),
                    $container->get(static::CONFIG_USER_RESOLVER_PASSWORD)
                );
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(): array
    {
        return [
            MiddlewareDispatcherInterface::class => function (
                ContainerInterface $container,
                MiddlewareDispatcherInterface $dispatcher
            ) {
                $dispatcher->use(
                    $container->get(static::CONFIG_TYPE) === 'basic'
                        ? BasicAuthenticationMiddleware::class
                        : DigestAuthenticationMiddleware::class
                );
            },
        ];
    }
}
