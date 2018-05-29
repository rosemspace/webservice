<?php

namespace Rosem\Authentication\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\Config\ConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareDispatcherInterface;
use Rosem\Authentication\Middleware\{
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
            MiddlewareDispatcherInterface::class => function (
                ContainerInterface $container,
                MiddlewareDispatcherInterface $app
            ) {
                if ($container->get(ConfigInterface::class)->get('authentication.http.type', 'digest') ===
                    'basic'
                ) {
                    $app->use(BasicAuthenticationMiddleware::class);
                } else {
                    $app->use(DigestAuthenticationMiddleware::class);
                }
            },
        ];
    }
}
