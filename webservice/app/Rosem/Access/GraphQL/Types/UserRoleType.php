<?php

namespace Rosem\Access\GraphQL\Types;

use Psrnext\GraphQL\{
    AbstractObjectType, TypeRegistryInterface
};

class UserRoleType extends AbstractObjectType
{
    public function getName(): string
    {
        return 'UserRole';
    }

    public function getDescription(): string
    {
        return 'The role of the user';
    }

    public function getDefaultFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'   => [
                'type'        => $typeRegistry->get('ID'),
                'description' => 'The id of the user role',
            ],
            'name' => [
                'type'        => $typeRegistry->nonNull($typeRegistry->get('String')),
                'description' => 'The name of the user role',
            ],
        ];
    }
}
