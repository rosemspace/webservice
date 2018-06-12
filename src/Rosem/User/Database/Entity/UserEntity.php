<?php

namespace Rosem\User\Database\Entity;

class UserEntity extends \Spot\Entity
{
    protected static $table = 'user';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'autoincrement' => true, 'primary' => true],
            'email'        => ['type' => 'string', 'required' => true],
            'first_name'   => ['type' => 'string', 'required' => true],
            'last_name'    => ['type' => 'string', 'required' => true],
        ];
    }
}
