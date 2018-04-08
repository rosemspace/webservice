<?php

namespace Rosem\Access\Http\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Psr\Container\ContainerInterface;
use Psrnext\GraphQL\AbstractQuery;

class UpdateUserMutation extends AbstractQuery
{
    public function description(): string
    {
        return 'Update the user';
    }

    public function type(ContainerInterface $container)
    {
        return Type::nonNull($container->get('User'));
    }

    public function args() : array
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
            'id' => 0,
            'firstName' => 'SUCCESS',
        ];
    }
}
