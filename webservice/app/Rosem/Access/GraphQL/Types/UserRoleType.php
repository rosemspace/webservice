<?php

namespace Rosem\Access\GraphQL\Types;

use Psrnext\GraphQL\{
    AbstractObjectType, TypeRegistryInterface
};

class UserRoleType extends AbstractObjectType
{
    public function getDescription(): string
    {
        return 'The role of the user';
    }

    public function getBaseFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'   => [
                'type'        => $typeRegistry->id(),
                'description' => 'The id of the user role',
            ],
            'name' => [
                'type'        => $typeRegistry->nonNull($typeRegistry->string()),
                'description' => 'The name of the user role',
            ],
        ];
    }
}
