<?php

namespace Rosem\Access\GraphQL\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;
use Psrnext\GraphQL\{
    AbstractQuery, TypeRegistryInterface
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
            'id'    => $registry->id(),
            'email' => $registry->string(),
        ];
    }

    public function resolve($source, $args, $container, ResolveInfo $info)
    {
        /** @var ContainerInterface $container $users */
        $users = $container->get(\Spot\Locator::class)->mapper(\Rosem\User\Database\Entity\UserEntity::class)->all();

//        foreach ($users as $user) {
//            var_dump($user->getFirstName());
//        }

//        var_dump($users[0]->getFirstName()); die;

        return $users;
    }
}
