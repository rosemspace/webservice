<?php

namespace Rosem\Access\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use True\GraphQL\AbstractObjectType;

class UserRoleType extends AbstractObjectType
{
    public function fields() : array
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
