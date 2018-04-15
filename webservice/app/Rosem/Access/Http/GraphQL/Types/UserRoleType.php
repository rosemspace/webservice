<?php

namespace Rosem\Access\Http\GraphQL\Types;

use GraphQL\Type\Definition\Type;
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

    public function getFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'   => [
                'type'        => Type::id(),
                'description' => 'The id of the user role',
            ],
            'name' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'The name of the user role',
            ],
        ] + $this->fields;
    }
}
