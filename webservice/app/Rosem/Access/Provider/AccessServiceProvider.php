<?php

namespace Rosem\Access\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\GraphQL\{
    GraphInterface, ObjectTypeInterface, QueryInterface
};
use Rosem\Access\Http\GraphQL\Types\{
    UserRoleType, UserType
};

class AccessServiceProvider implements ServiceProviderInterface
{
    public function getFactories(): array
    {
        return [
            UserType::class     => function (): ObjectTypeInterface {
                return new \Rosem\Access\Http\GraphQL\Types\UserType;
            },
            UserRoleType::class => function (): ObjectTypeInterface {
                return new \Rosem\Access\Http\GraphQL\Types\UserRoleType;
            },
        ];
    }

    public function getExtensions(): array
    {
        return [
            GraphInterface::class => function (ContainerInterface $container, GraphInterface $graph) {
                $graph->schema()->query('users', function (): QueryInterface {
                    return new \Rosem\Access\Http\GraphQL\Queries\UsersQuery;
                });
                $graph->schema()->mutation('updateUser', function (): QueryInterface {
                    return new \Rosem\Access\Http\GraphQL\Mutations\UpdateUserMutation;
                });
            },
        ];
    }
}
