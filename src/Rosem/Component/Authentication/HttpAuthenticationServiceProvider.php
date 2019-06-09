<?php

namespace Rosem\Component\Authentication;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\Authentication\Http\Server\{
    BasicAuthenticationMiddleware, DigestAuthenticationMiddleware
};
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;

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
            MiddlewareCollectorInterface::class => function (
                ContainerInterface $container,
                MiddlewareCollectorInterface $middlewareCollector
            ) {
                $middlewareCollector->addDeferredMiddleware(
                    $container->get(static::CONFIG_TYPE) === 'basic'
                        ? BasicAuthenticationMiddleware::class
                        : DigestAuthenticationMiddleware::class
                );
            },
        ];
    }
}
