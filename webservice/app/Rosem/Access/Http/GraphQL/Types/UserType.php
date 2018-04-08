<?php

namespace Rosem\Access\Http\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Psr\Container\ContainerInterface;
use Psrnext\GraphQL\AbstractObjectType;

class UserType extends AbstractObjectType
{
    public function description(): string
    {
        return 'The user';
    }

    public function fields(ContainerInterface $container) : array
    {
        return [
            'id'        => [
                'type'        => Type::id(),
                'description' => 'The id of the user',
            ],
            'firstName' => [
                'type'        => Type::string(),
                'description' => 'The first name of the user',
            ],
            'lastName'  => [
                'type'        => Type::string(),
                'description' => 'The last name of the user',
            ],
            'email'     => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'The email of the user',
            ],
            'role'      => [
                'type'        => Type::nonNull($container->get('UserRole')),
                'description' => 'The role of the user',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The time when the user was created',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The time when the user was updated',
            ],
            'deleted_at' => [
                'type' => Type::string(),
                'description' => 'The time when the user was deleted',
            ],
        ];
    }
}
