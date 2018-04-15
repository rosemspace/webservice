<?php

namespace Rosem\Access\Http\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Psrnext\GraphQL\{
    ObjectTypeInterface, TypeRegistryInterface
};

class UserType implements ObjectTypeInterface
{
    public function getName(): string
    {
        return 'User';
    }

    public function getDescription(): string
    {
        return 'The user';
    }

    public function getFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'         => [
                'type'        => Type::id(),
                'description' => 'The id of the user',
            ],
            'firstName'  => [
                'type'        => Type::string(),
                'description' => 'The first name of the user',
            ],
            'lastName'   => [
                'type'        => Type::string(),
                'description' => 'The last name of the user',
            ],
            'email'      => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'The email of the user',
            ],
            'role'       => [
                'type'        => Type::nonNull($typeRegistry->get(UserRoleType::class)),
                'description' => 'The role of the user',
            ],
            'created_at' => [
                'type'        => Type::string(),
                'description' => 'The time when the user was created',
            ],
            'updated_at' => [
                'type'        => Type::string(),
                'description' => 'The time when the user was updated',
            ],
            'deleted_at' => [
                'type'        => Type::string(),
                'description' => 'The time when the user was deleted',
            ],
        ];
    }
}
