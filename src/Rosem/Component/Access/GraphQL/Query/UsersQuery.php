<?php

namespace Rosem\Component\Access\GraphQL\Query;

use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;
use Rosem\Contract\GraphQL\{
    AbstractQuery,
    TypeRegistryInterface
};
use Rosem\Component\Access\GraphQL\Type\UserType;

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
            ->getRepository(\Rosem\Component\Access\Entity\User::class)
            ->findAll();

        return $users;
    }
}
