<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;

class AuthenticationProvider implements ServiceProviderInterface
{
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
            'auth.symmetricKey' => function () {
//                return null;
                return 'mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw=';
            },
            'auth.userPasswordGetter' => function () {
//                return null;
                return function (string $username): ?string {
                    return ['roshe' => '1234'][$username] ?? null;
                };
            },
            'auth.uri' => function () {
                return '/login';
            },
            SessionMiddleware::class        => function (ContainerInterface $container) {
                return SessionMiddleware::fromSymmetricKeyDefaults(
                    $container->get('auth.symmetricKey'),
                    20 * 60 // 20 minutes
                );
            },
            AuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new AuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get('auth.userPasswordGetter'),
                    $container->get('auth.uri')
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
                MiddlewareQueueInterface $middlewareDispatcher
            ) {
                $middlewareDispatcher->use(SessionMiddleware::class);
            },
        ];
    }
}
