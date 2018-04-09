<?php

namespace Rosem\Access\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\GraphQL\GraphInterface;
use Psrnext\GraphQL\NodeInterface;

class AccessServiceProvider implements ServiceProviderInterface
{
    public function getFactories(): array
    {
        return [
            'User'     => function (): NodeInterface {
                return new \Rosem\Access\Http\GraphQL\Types\UserType;
            },
            'UserRole' => function (): NodeInterface {
                return new \Rosem\Access\Http\GraphQL\Types\UserRoleType;
            },
        ];
    }

    public function getExtensions(): array
    {
        return [
            GraphInterface::class => function (ContainerInterface $container, GraphInterface $graph) {
                $graph->schema()->query('users', function (): NodeInterface {
                    return new \Rosem\Access\Http\GraphQL\Queries\UsersQuery;
                });
                $graph->schema()->mutation('updateUser', function (): NodeInterface {
                    return new \Rosem\Access\Http\GraphQL\Mutations\UpdateUserMutation;
                });
            },
        ];
    }
}
