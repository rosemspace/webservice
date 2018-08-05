<?php

namespace Rosem\Access\GraphQL\Type;

use Rosem\Psr\GraphQL\{
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
            'id'   => self::field(
                $typeRegistry->id(),
                'The id of the user role'
            ),
            'name' => self::field(
                $typeRegistry->nonNull($typeRegistry->string()),
                'The name of the user role'
            ),
        ];
    }
}
