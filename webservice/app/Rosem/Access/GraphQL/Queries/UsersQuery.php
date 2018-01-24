<?php

namespace Rosem\Access\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rosem\Access\Database\Models\User;
use True\GraphQL\AbstractQuery;

class UsersQuery extends AbstractQuery
{
    protected $model = User::class;

    public function type()
    {
        return Type::nonNull(Type::listOf(Type::nonNull($this->graph->getType('User'))));
    }

    public function args() : array
    {
        return [
            'id' => Type::id(),
            'email' => Type::string(),
        ];
    }
}
