<?php

namespace Rosem\Access\Http\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rosem\Access\Database\Models\User;
use TrueStandards\GraphQL\AbstractQuery;
use TrueStandards\GraphQL\GraphInterface;
use GraphQL\Type\Definition\ResolveInfo;

class UsersQuery extends AbstractQuery
{
    public $name = 'users';

    public $description = 'Users query';

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
