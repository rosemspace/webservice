<?php

namespace Rosem\Component\Authentication\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\Authentication\Middleware\{
    AbstractAuthenticationMiddleware,
    HttpAuthenticationMiddleware
};
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;

class HttpAuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_TYPE = 'auth.http.type';

    public const CONFIG_USER_PASSWORD_RESOLVER = 'auth.http.user.passwordResolver';

    public const CONFIG_USER_LIST = 'auth.http.user.list';

    public const CONFIG_REALM = 'auth.http.realm';

    public const CONFIG_NONCE = 'auth.http.nonce';

    /**
     * @inheritdoc
     */
    public function getFactories(): array
    {
        return [
            //@TODO constants
            static::CONFIG_REALM => static fn(ContainerInterface $container) => $container->has('AUTH_HTTP_REALM')
                ? $container->get('AUTH_HTTP_REALM')
                : $container->get('APP_NAME'),
            static::CONFIG_NONCE => static fn(): string => '',
            static::CONFIG_TYPE => static fn(): string => 'digest',
            static::CONFIG_USER_PASSWORD_RESOLVER => static fn(ContainerInterface $container
            ): callable => static fn(string $username): ?string => $container->get(
                    static::CONFIG_USER_LIST
                )[$username] ?? null,
            HttpAuthenticationMiddleware::class => static fn(ContainerInterface $container
            ): AbstractAuthenticationMiddleware => (new HttpAuthenticationMiddleware(
                $container->get(ResponseFactoryInterface::class),
                $container->get(UserFactoryInterface::class),
                $container->get(static::CONFIG_USER_PASSWORD_RESOLVER),
                $container->get(static::CONFIG_REALM),
                $container->get(static::CONFIG_NONCE),
                $container->get(static::CONFIG_TYPE)
            ))->delegateMiddleware,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getExtensions(): array
    {
        return [
            MiddlewareCollectorInterface::class => static function (
                ContainerInterface $container,
                MiddlewareCollectorInterface $middlewareCollector
            ): void {
                $middlewareCollector->addMiddleware($container->get(HttpAuthenticationMiddleware::class));
            },
        ];
    }
}
