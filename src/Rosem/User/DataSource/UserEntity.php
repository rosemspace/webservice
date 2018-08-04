<?php

namespace Rosem\User\DataSource;

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

    public function getFirstName(): string
    {
        return $this->get('first_name');
    }

    public function getLastName(): string
    {
        return $this->get('last_name');
    }
}
