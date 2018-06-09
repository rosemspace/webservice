<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;
use Rosem\Authentication\Http\Server\{
    BasicAuthenticationMiddleware, DigestAuthenticationMiddleware
};

class HttpAuthenticationProvider implements ServiceProviderInterface
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
            BasicAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new BasicAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    function (string $username): ?string {
                        return ['roshe' => '1111'][$username] ?? null;
                    }
                );
            },
            DigestAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new DigestAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    function (string $username): ?string {
                        return ['roshe' => '1111'][$username] ?? null;
                    }
                );
            },
            'authentication.http.type' => function () {
                return 'digest';
            }
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
                MiddlewareQueueInterface $app
            ) {
                if ($container->get('authentication.http.type') === 'basic') {
                    $app->use(BasicAuthenticationMiddleware::class);
                } else {
                    $app->use(DigestAuthenticationMiddleware::class);
                }
            },
        ];
    }
}
