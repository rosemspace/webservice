<?php

namespace Rosem\Access\Http\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use TrueStandards\GraphQL\AbstractObjectType;

class UserRoleType extends AbstractObjectType
{
    public $name = 'UserRole';

    public $description = 'Role type of the user';

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
