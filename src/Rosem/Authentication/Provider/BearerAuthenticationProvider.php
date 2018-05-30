<?php

namespace Rosem\Authentication\Provider;

use Psr\Container\ContainerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareProcessorInterface;
use Rosem\Authentication\Middleware\BearerAuthenticationMiddleware;

class BearerAuthenticationProvider implements ServiceProviderInterface
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
            SessionMiddleware::class => function () {
                return SessionMiddleware::fromSymmetricKeyDefaults(
                    'mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw=',
                    20 * 60 // 20 minutes
                );
            },
            BearerAuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new BearerAuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    function (string $username): ?string {
                        return ['roshe' => '1234'][$username] ?? null;
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
//            RouteCollectorInterface::class => function (
//                ContainerInterface $container,
//                RouteCollectorInterface $routeCollector
//            ) {
//                $routeCollector->get(
//                    "/admin",
//                    [BearerAuthenticationMiddleware::class, 'process']
//                );
//            },
            MiddlewareProcessorInterface::class => function (
                ContainerInterface $container,
                MiddlewareProcessorInterface $middlewareDispatcher
            ) {
                $middlewareDispatcher->use(SessionMiddleware::class);
            },
        ];
    }
}
