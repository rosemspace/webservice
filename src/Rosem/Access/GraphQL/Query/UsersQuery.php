<?php

namespace Rosem\Access\GraphQL\Query;

use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;
use Rosem\Psr\GraphQL\{
    AbstractQuery,
    TypeRegistryInterface
};
use Rosem\Access\GraphQL\Type\UserType;

class UsersQuery extends AbstractQuery
{
    public function getDescription(): string
    {
        return 'Fetch user collection';
    }

    public function getType(TypeRegistryInterface $registry)
    {
        return $registry->nonNull(
            $registry->listOf($registry->nonNull($registry->get(UserType::class)))
        );
    }

    public function getBaseArguments(TypeRegistryInterface $registry): array
    {
        return [
            'id' => $registry->id(),
            'email' => $registry->string(),
        ];
    }

    public function resolve($source, $args, $container, ResolveInfo $info)
    {
        /** @var ContainerInterface $container */
        $users = $container->get(EntityManager::class)
            ->getRepository(\Rosem\Access\Entity\User::class)
            ->findAll();

        return $users;
    }
}
