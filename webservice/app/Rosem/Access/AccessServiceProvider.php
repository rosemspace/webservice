<?php

namespace Rosem\Access;

use TrueStd\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use TrueStandards\GraphQL\GraphInterface;
use Rosem\Access\GraphQL\{
    Types, Queries, Mutations
};

class AccessServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     */
    public function getExtensions() : array
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
