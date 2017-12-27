<?php

namespace Rosem\Access\Http\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use TrueStandards\GraphQL\AbstractObjectType;

class UserType extends AbstractObjectType
{
//    const name = 'User';

    public $name = 'User';

    public $description = 'User type';

    public function fields() : array
    {
        return [
            'id'        => self::field(Type::id(), 'The id of the user'),
            'firstName' => self::field(Type::string(), 'The first name of the user'),
            'lastName'  => self::field(Type::string(), 'The last name of the user'),
            'email'     => self::field(Type::nonNull(Type::string()), 'The email of the user'),
            'role'      => self::field(
                Type::nonNull($this->type('UserRole')),
                'The role of the user'
            ),
        ];
    }
}
