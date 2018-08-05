<?php

namespace Rosem\Access\Provider;

use Psr\Container\ContainerInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\GraphQL\{
    GraphInterface, ObjectTypeInterface, QueryInterface
};
use Rosem\Access\GraphQL\Mutation\UpdateUserMutation;
use Rosem\Access\GraphQL\Query\LoginQuery;
use Rosem\Access\GraphQL\Query\UsersQuery;
use Rosem\Access\GraphQL\Type\{
    UserRoleType, UserType
};

class AccessServiceProvider implements ServiceProviderInterface
{
    public function getFactories(): array
    {
        return [
            UserType::class => function (): ObjectTypeInterface {
                return new UserType();
            },
            UserRoleType::class => function (): ObjectTypeInterface {
                return new UserRoleType();
            },
        ];
    }

    public function getExtensions(): array
    {
        return [
            GraphInterface::class => function (ContainerInterface $container, GraphInterface $graph) {
                $graph->getSchema('default')->query('login', function (): QueryInterface {
                    return new LoginQuery();
                });
                $graph->getSchema('default')->query('users', function (): QueryInterface {
                    return new UsersQuery();
                });
                $graph->getSchema('default')->mutation('updateUser', function (): QueryInterface {
                    return new UpdateUserMutation();
                });
            },
        ];
    }
}
