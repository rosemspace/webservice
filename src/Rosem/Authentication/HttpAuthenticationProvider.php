<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Psr\Http\Server\MiddlewareQueueInterface;
use Rosem\Authentication\Http\Server\{
    BasicAuthenticationMiddleware, DigestAuthenticationMiddleware
};

class HttpAuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_TYPE = 'auth.http.type';

    public const CONFIG_USER_PASSWORD_GETTER = 'auth.http.userPasswordGetter';

    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_TYPE => function () {
                return 'digest';
            },
            static::CONFIG_USER_PASSWORD_GETTER => function () {
//                return null;
                return function (string $username): ?string {
                    return ['roshe' => '1111'][$username] ?? null;
                };
            },
            BasicAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new BasicAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(static::CONFIG_USER_PASSWORD_GETTER)
                );
            },
            DigestAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new DigestAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(static::CONFIG_USER_PASSWORD_GETTER)
                );
            },
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     * About factories parameters:
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null`
     * will be passed.
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [
            MiddlewareQueueInterface::class => function (
                ContainerInterface $container,
                MiddlewareQueueInterface $middlewareQueue
            ) {
                $middlewareQueue->add(
                    $container->get(static::CONFIG_TYPE) === 'basic'
                        ? BasicAuthenticationMiddleware::class
                        : DigestAuthenticationMiddleware::class
                );
            },
        ];
    }
}
