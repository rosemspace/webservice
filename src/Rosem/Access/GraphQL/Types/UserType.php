<?php

namespace Rosem\Access\GraphQL\Types;

use Psrnext\GraphQL\{
    AbstractObjectType, TypeRegistryInterface
};

class UserType extends AbstractObjectType
{
    public function getDescription(): string
    {
        return 'The user';
    }

    public function getBaseFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'         => [
                'type'        => $typeRegistry->id(),
                'description' => 'The id of the user',
            ],
            'firstName'  => [
                'type'        => $typeRegistry->string(),
                'description' => 'The first name of the user',
            ],
            'lastName'   => [
                'type'        => $typeRegistry->string(),
                'description' => 'The last name of the user',
            ],
            'email'      => [
                'type'        => $typeRegistry->nonNull($typeRegistry->string()),
                'description' => 'The email of the user',
            ],
            'role'       => [
                'type'        => $typeRegistry->nonNull($typeRegistry->get(UserRoleType::class)),
                'description' => 'The role of the user',
            ],
            'createdAt' => [
                'type'        => $typeRegistry->string(),
                'description' => 'The time when the user was created',
            ],
            'updatedAt' => [
                'type'        => $typeRegistry->string(),
                'description' => 'The time when the user was updated',
            ],
            'deletedAt' => [
                'type'        => $typeRegistry->string(),
                'description' => 'The time when the user was deleted',
            ],
        ];
    }
}
