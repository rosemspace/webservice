<?php

namespace Rosem\Access\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rosem\Access\GraphQL\Queries\UsersQuery;

class UpdateUserMutation extends UsersQuery
{
    public function type()
    {
        return Type::nonNull($this->graph->getType('User'));
    }

    public function args() : array
    {
        return [
            'id'        => Type::id(),
            'email'     => Type::string(),
            'firstName' => Type::string(),
            'lastName'  => Type::string(),
            'password'  => Type::string(),
        ];
    }

    public function resolve($source, $args, $context, ResolveInfo $info)
    {
        /** @var \Rosem\Access\Database\Models\User $user */
        $user = $this->mapper->find($args['id']);
        unset($args['id']);
        $user->fill($args);
        try {
            $this->mapper->store($user);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $user;
    }
}
