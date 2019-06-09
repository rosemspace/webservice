<?php

namespace Rosem\Component\Access\GraphQL\Type;

use Rosem\Contract\GraphQL\{
    AbstractObjectType, TypeRegistryInterface
};

class UserRoleType extends AbstractObjectType
{
    public function getDescription(): string
    {
        return 'User role';
    }

    public function getBaseFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'   => self::field(
                $typeRegistry->id(),
                'User role ID'
            ),
            'name' => self::field(
                $typeRegistry->nonNull($typeRegistry->string()),
                'User role name'
            ),
        ];
    }
}
