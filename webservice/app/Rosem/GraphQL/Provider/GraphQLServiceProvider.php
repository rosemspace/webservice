<?php

namespace Rosem\GraphQL\Provider;

use GraphQL\Server\StandardServer;
use Psr\Container\ContainerInterface;
use Psrnext\{
    App\AppConfigInterface, Container\ServiceProviderInterface, Environment\EnvironmentInterface, GraphQL\GraphInterface
};
use Rosem\GraphQL\Middleware\GraphQLMiddleware;

class GraphQLServiceProvider implements ServiceProviderInterface
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
            GraphQLMiddleware::class => function (ContainerInterface $container) {
                return new GraphQLMiddleware(
                    new StandardServer([
                        'schema'  => $container->get(GraphInterface::class)->schema()->create(),
                        'context' => $container,
                    ]),
                    $container->get(AppConfigInterface::class)->get('api.uri'),
                    $container->get(EnvironmentInterface::class)->isDevelopmentMode()
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
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null` will be passed.
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [];
    }
}
