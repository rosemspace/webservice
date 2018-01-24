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

    public function getIdAttribute($value)
    {
        return (int) $value;
    }

//    public function setSlugAttribute($value) {
//
//        if (! $value) {
//            $this->slug = str_slug($this->title);
//        }
//    }

    /**
     * Always capitalize the first name when we save it to the database
     */
    public function setFirstNameAttribute($value) {
        return ucfirst($value);
    }

    /**
     * Always capitalize the last name when we save it to the database
     */
    public function setLastNameAttribute($value) {
        return ucfirst($value);
    }

    public function setPasswordAttribute($value) {
        return password_hash($value, PASSWORD_DEFAULT);
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

    /**
     * Magic __isset method to check for properties in camelCase style.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return parent::__isset(snake_case($key));
    }
    /**
     * Magic __unset method to delete properties in camelCase style.
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        parent::__unset(snake_case($key));
    }

    /**
     * Fill an entity with key-value pairs.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            $key = snake_case($key);

            if ($this->hasSetMutator($key)) {
                $method = 'set'.$this->getMutatorMethod($key);
                $this->attributes[$key] = $this->$method($attribute);
            } else {
                $this->attributes[$key] = $attribute;
            }
        }
    }
}
