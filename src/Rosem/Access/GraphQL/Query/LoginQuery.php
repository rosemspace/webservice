<?php

namespace Rosem\Access\GraphQL\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Psrnext\GraphQL\AbstractQuery;
use Psrnext\GraphQL\TypeRegistryInterface;
use Rosem\Access\GraphQL\Type\UserType;

class LoginQuery extends AbstractQuery
{
    public function getBaseArguments(TypeRegistryInterface $registry)
    {
        return [
            'username' => $registry->string(),
            'password' => $registry->string(),
        ];
    }

    public function getType(TypeRegistryInterface $registry)
    {
        return $registry->nonNull($registry->get(UserType::class));
    }

    public function resolve($source, $args, $context, ResolveInfo $resolveInfo)
    {
        $users = ['roshe' => '1234'];

        if (isset($users[$args['username']]) && $users[$args['username']] === $args['password']) {
            return [
                'id' => '1',
                'firstName' => 'Roman',
                'lastName' => 'Shevchenko',
                'email' => 'iroman.via@gmail.com',
            ];
        }

        return null;
    }
}
