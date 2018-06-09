<?php

namespace Rosem\Access\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\GraphQL\{
    GraphInterface, ObjectTypeInterface, QueryInterface
};
use Rosem\Access\GraphQL\Mutation\UpdateUserMutation;
use Rosem\Access\GraphQL\Query\UsersQuery;
use Rosem\Access\GraphQL\Type\{
    UserRoleType, UserType
};

class AccessServiceProvider implements ServiceProviderInterface
{
    public function getFactories() : array
    {
        return [
            UserType::class     => function (): ObjectTypeInterface {
                return new UserType;
            },
            UserRoleType::class => function (): ObjectTypeInterface {
                return new UserRoleType;
            },
        ];
    }

    public function getExtensions() : array
    {
        return [
            GraphInterface::class => function (ContainerInterface $container, GraphInterface $graph) {
                $graph->schema()->query('users', function (): QueryInterface {
                    return new UsersQuery;
                });
                $graph->schema()->mutation('updateUser', function (): QueryInterface {
                    return new UpdateUserMutation;
                });
            },
        ];
    }
}
