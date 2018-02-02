<?php

use GraphQL\Type\Definition\Type;

class User
{
    private $id;

    private $first_name;

    private $last_name;

    private $email;

    private $password;

    private $role;

    public static function fields(True\GraphQL\Graph $graph) : array
    {
        return [
            'id'        => [
                'type'        => Type::id(),
                'description' => 'The id of the user',
            ],
            'firstName' => [
                'type'        => Type::string(),
                'description' => 'The first name of the user',
            ],
            'lastName'  => [
                'type'        => Type::string(),
                'description' => 'The last name of the user',
            ],
            'email'     => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'The email of the user',
            ],
            'role'      => [
                'type'        => Type::nonNull($graph->getType('UserRole')),
                'description' => 'The role of the user',
            ],
            'createdAt' => [
                'type' => Type::string(),
                'description' => 'The time when the user was created',
            ],
            'updatedAt' => [
                'type' => Type::string(),
                'description' => 'The time when the user was updated',
            ],
            'deletedAt' => [
                'type' => Type::string(),
                'description' => 'The time when the user was deleted',
            ],
        ];
    }

    public function setFirstName(string $firstName)
    {
        $this->first_name = $firstName;
    }

    public function setLastName(string $lastName)
    {
        $this->last_name = $lastName;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setPassword(string $password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setRole(UserRole $role)
    {
        $this->role = $role;
    }
}
