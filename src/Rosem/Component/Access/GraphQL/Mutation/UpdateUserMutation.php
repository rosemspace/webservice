<?php

namespace Rosem\Component\Access\GraphQL\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use Rosem\Contract\GraphQL\{
    AbstractQuery, TypeRegistryInterface
};
use Rosem\Component\Access\GraphQL\Type\UserType;

class UpdateUserMutation extends AbstractQuery
{
    public function getDescription(): string
    {
        return 'Update the user';
    }

    public function getType(TypeRegistryInterface $registry)
    {
        return $registry->nonNull($registry->get(UserType::class));
    }

    public function getBaseArguments(TypeRegistryInterface $registry): array
    {
        return [
            'id'        => $registry->id(),
            'email'     => $registry->string(),
            'firstName' => $registry->string(),
            'lastName'  => $registry->string(),
            'password'  => $registry->string(),
        ];
    }

    public function resolve($source, $args, $context, ResolveInfo $info)
    {
//        /** @var \Rosem\Component\Access\Entity\User $user */
//        $user = $this->mapper->find($args['id']);
//        unset($args['id']);
//        $user->fill($args);
//        try {
//            $this->mapper->store($user);
//        } catch (\Exception $e) {
//            echo $e->getMessage();
//        }
//
//        return $user;

        return [
            'id'        => 0,
            'firstName' => 'SUCCESS',
        ];
    }
}
