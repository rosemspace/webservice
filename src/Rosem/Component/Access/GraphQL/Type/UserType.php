<?php

namespace Rosem\Component\Access\GraphQL\Type;

use Rosem\Contract\GraphQL\{
    AbstractObjectType, TypeRegistryInterface
};

class UserType extends AbstractObjectType
{
    public function getDescription(): string
    {
        return 'The user';
    }

    public function getBaseFields(TypeRegistryInterface $typeRegistry): array
    {
        return [
            'id'        => self::field(
                $typeRegistry->id(),
                'User ID'
            ),
            'firstName' => self::field(
                $typeRegistry->string(),
                'The first name of the user'
            ),
            'lastName'  => self::field(
                $typeRegistry->string(),
                'The last name of the user'
            ),
            'slug'      => self::field(
                $typeRegistry->nonNull($typeRegistry->string()),
                'The slug of the user'
            ),
            'email'     => self::field(
                $typeRegistry->nonNull($typeRegistry->string()),
                'The email of the user'
            ),
            'role'      => self::field(
                $typeRegistry->nonNull($typeRegistry->get(UserRoleType::class)),
                'The role of the user'
            ),
            'createdAt' => self::field(
                $typeRegistry->string(),
                'The time when the user was created'
            ),
            'updatedAt' => self::field(
                $typeRegistry->string(),
                'The time when the user was updated'
            ),
            'deletedAt' => self::field(
                $typeRegistry->string(),
                'The time when the user was deleted'
            ),
        ];
    }
}
