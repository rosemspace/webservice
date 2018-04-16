<?php

namespace Rosem\Access\GraphQL\Queries;

use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\{
    ResolveInfo, Type
};
use Psrnext\GraphQL\{
    AbstractQuery, TypeRegistryInterface
};
use Rosem\Access\GraphQL\Types\UserType;

class UsersQuery extends AbstractQuery
{
    public function getDescription(): string
    {
        return 'Fetch user collection';
    }

    public function getType(TypeRegistryInterface $typeRegistry)
    {
        return Type::nonNull(Type::listOf(Type::nonNull($typeRegistry->get(UserType::class))));
    }

    public function getDefaultArguments(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'    => Type::id(),
            'email' => Type::string(),
        ];
    }

    public function resolve($source, $args, $container, ResolveInfo $info)
    {
        $users = $container->get(EntityManager::class)->getRepository(\Rosem\Access\Entity\User::class)->findAll();

        return $users;
    }
}
