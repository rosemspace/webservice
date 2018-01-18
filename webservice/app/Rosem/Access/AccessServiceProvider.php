<?php

namespace Rosem\Access;

use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use TrueStandards\GraphQL\GraphInterface;
use Rosem\Access\GraphQL\{
    Types, Queries, Mutations
};

class AccessServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     *
     * @return callable[]
     */
    public function getFactories()
    {
        return [];
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
     *
     * @return callable[]
     */
    public function getExtensions()
    {
        return [
            GraphInterface::class => function (ContainerInterface $container, GraphInterface $graph) {
                $graph->addType(
                    Types\UserType::class,
                    'User',
                    'The user'
                );
                $graph->addType(
                    Types\UserRoleType::class,
                    'UserRole',
                    'The role of the user'
                );
                $graph->addQuery(
                    Queries\UsersQuery::class,
                    'users',
                    'Users query'
                );
                $graph->addMutation(
                    Mutations\UpdateUserMutation::class,
                    'updateUser',
                    'Update the user'
                );

                return $graph;
            },
        ];
    }
}
