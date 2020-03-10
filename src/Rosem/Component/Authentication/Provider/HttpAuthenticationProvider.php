<?php

namespace Rosem\Component\Authentication\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Component\Authentication\Middleware\{
    BasicAuthenticationMiddleware,
    DigestAuthenticationMiddleware
};
use Rosem\Contract\App\AppInterface;
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;

class HttpAuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_TYPE = 'auth.http.type';

    public const CONFIG_USER_PASSWORD_RESOLVER = 'auth.http.user.passwordResolver';

    public const CONFIG_USER_LIST = 'auth.http.user.list';

    public const CONFIG_REALM = 'auth.http.realm';

    /**
     * @inheritdoc
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_REALM => static fn(AppInterface $app) => null, //$app->getEnv('AUTH_HTTP_REALM');,
            static::CONFIG_TYPE => static fn(): string => 'digest',
            static::CONFIG_USER_PASSWORD_RESOLVER => static function (ContainerInterface $container): callable {
                return static function (string $username) use (&$container): ?string {
                    return $container->get(static::CONFIG_USER_LIST)[$username] ?? null;
                };
            },
            BasicAuthenticationMiddleware::class => static fn(ContainerInterface $container
            ): BasicAuthenticationMiddleware => new BasicAuthenticationMiddleware(
                $container->get(ResponseFactoryInterface::class),
                $container->get(UserFactoryInterface::class),
                $container->get(static::CONFIG_USER_PASSWORD_RESOLVER),
                $container->get(static::CONFIG_REALM)
            ),
            DigestAuthenticationMiddleware::class => static fn(ContainerInterface $container
            ): DigestAuthenticationMiddleware => new DigestAuthenticationMiddleware(
                $container->get(ResponseFactoryInterface::class),
                $container->get(UserFactoryInterface::class),
                $container->get(static::CONFIG_USER_PASSWORD_RESOLVER),
                $container->get(static::CONFIG_REALM)
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getExtensions(): array
    {
        return [
            MiddlewareCollectorInterface::class => static fn(
                ContainerInterface $container,
                MiddlewareCollectorInterface $middlewareCollector
            ) => $middlewareCollector->addDeferredMiddleware(
                $container->get(static::CONFIG_TYPE) === 'basic'
                    ? BasicAuthenticationMiddleware::class
                    : DigestAuthenticationMiddleware::class
            ),
        ];
    }
}
