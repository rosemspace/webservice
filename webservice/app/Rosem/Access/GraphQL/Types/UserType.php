<?php

namespace Rosem\Access\GraphQL\Types;

use Psrnext\GraphQL\{
    AbstractObjectType, TypeRegistryInterface
};

class UserType extends AbstractObjectType
{
    public function getName(): string
    {
        return 'User';
    }

    public function getDescription(): string
    {
        return 'The user';
    }

    public function getDefaultFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'         => [
                'type'        => $typeRegistry->get('ID'),
                'description' => 'The id of the user',
            ],
            'firstName'  => [
                'type'        => $typeRegistry->get('String'),
                'description' => 'The first name of the user',
            ],
            'lastName'   => [
                'type'        => $typeRegistry->get('String'),
                'description' => 'The last name of the user',
            ],
            'email'      => [
                'type'        => $typeRegistry->nonNull($typeRegistry->get('String')),
                'description' => 'The email of the user',
            ],
            'role'       => [
                'type'        => $typeRegistry->nonNull($typeRegistry->get(UserRoleType::class)),
                'description' => 'The role of the user',
            ],
            'createdAt' => [
                'type'        => $typeRegistry->get('String'),
                'description' => 'The time when the user was created',
            ],
            'updatedAt' => [
                'type'        => $typeRegistry->get('String'),
                'description' => 'The time when the user was updated',
            ],
            'deletedAt' => [
                'type'        => $typeRegistry->get('String'),
                'description' => 'The time when the user was deleted',
            ],
        ];
    }
}
