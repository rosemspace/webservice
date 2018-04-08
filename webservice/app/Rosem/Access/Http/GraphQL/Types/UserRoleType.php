<?php

namespace Rosem\Access\Http\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Psr\Container\ContainerInterface;
use Psrnext\GraphQL\AbstractObjectType;

class UserRoleType extends AbstractObjectType
{
    public function description(): string
    {
        return 'The role of the user';
    }

    public function fields(ContainerInterface $container): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The id of the user role'
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the user role'
            ],
        ];
    }
}
