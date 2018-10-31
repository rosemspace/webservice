<?php

namespace Rosem\Access\GraphQL\Query;

use Doctrine\ORM\EntityManager;
use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;
use Rosem\Access\Entity\User;
use Rosem\Psr\GraphQL\AbstractQuery;
use Rosem\Psr\GraphQL\TypeRegistryInterface;
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

    public function resolve($source, $args, $container, ResolveInfo $resolveInfo)
    {
        if (isset($args['username'])) {
            /** @var ContainerInterface $container */
            /** @var User? $user */
            $user = $container->get(EntityManager::class)
                ->getRepository(\Rosem\Access\Entity\User::class)
                ->findOneBy(['email' => $args['username']]);

            if (null !== $user && $user->getPassword() === $args['password']) {
                return $user;
            }
        }

        return null;
    }
}
