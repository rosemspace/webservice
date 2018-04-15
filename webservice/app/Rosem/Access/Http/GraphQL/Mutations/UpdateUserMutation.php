<?php

namespace Rosem\Access\Http\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Psrnext\GraphQL\{
    QueryInterface, TypeRegistryInterface
};
use Rosem\Access\Http\GraphQL\Types\UserType;

class UpdateUserMutation implements QueryInterface
{
    public function getDescription(): string
    {
        return 'Update the user';
    }

    public function getType(TypeRegistryInterface $typeRegistry)
    {
        return Type::nonNull($typeRegistry->get(UserType::class));
    }

    public function getArguments(): array
    {
        return [
            'id'        => Type::id(),
            'email'     => Type::string(),
            'firstName' => Type::string(),
            'lastName'  => Type::string(),
            'password'  => Type::string(),
        ];
    }

    public function resolve($source, $args, $context, ResolveInfo $info)
    {
//        /** @var \Rosem\Access\Entity\User $user */
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
