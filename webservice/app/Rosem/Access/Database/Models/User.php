<?php

namespace Rosem\Access\Database\Models;

use Analogue\ORM\Entity;

class User extends Entity
{
    const GUEST = 'guest';
    const ADMIN = 'admin';
    const RESIDENT = 'resident';

    public function __construct($firstName, $lastName, $email, $password, UserRole $role)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Return the entity's attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return parent::__get(snake_case($key));
    }

    /**
     * Dynamically set attributes on the entity.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        parent::__set(snake_case($key), $value);
    }
}
