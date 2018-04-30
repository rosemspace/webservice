<?php

namespace Rosem\Access\GraphQL\Queries;

use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\ResolveInfo;
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
        return $typeRegistry->nonNull(
            $typeRegistry->listOf($typeRegistry->nonNull($typeRegistry->get(UserType::class)))
        );
    }

    public function getBaseArguments(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'    => $typeRegistry->id(),
            'email' => $typeRegistry->string(),
        ];
    }

    public function resolve($source, $args, $container, ResolveInfo $info)
    {
        $users = $container->get(EntityManager::class)->getRepository(\Rosem\Access\Entity\User::class)->findAll();

        return $users;
    }
}
