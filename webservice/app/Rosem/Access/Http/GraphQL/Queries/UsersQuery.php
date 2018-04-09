<?php

namespace Rosem\Access\Http\GraphQL\Queries;

use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\{
    ResolveInfo, Type
};
use Psr\Container\ContainerInterface;
use Psrnext\GraphQL\AbstractQuery;

class UsersQuery extends AbstractQuery
{
    public function description(): string
    {
        return 'Fetch user collection';
    }

    public function type(ContainerInterface $container)
    {
        return Type::nonNull(Type::listOf(Type::nonNull($container->get('User'))));
    }

    public function args(): array
    {
        return [
            'id' => Type::id(),
            'email' => Type::string(),
        ];
    }

    public function resolve($source, $args, $container, ResolveInfo $info)
    {
        $users = $container->get(EntityManager::class)->getRepository(\Rosem\Access\Entity\User::class)->findAll();

        return $users;

//        return [
//            [
//                'id' => 1,
//                'firstName' => 'Roman',
//                'email' => 'roshe@smile.fr',
//            ],
//            [
//                'id' => 2,
//                'firstName' => 'Romanna',
//                'email' => 'rosem@smile.fr',
//            ],
//        ];
    }
}
