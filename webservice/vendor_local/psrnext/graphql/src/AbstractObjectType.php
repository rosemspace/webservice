<?php

namespace Psrnext\GraphQL;

use Closure;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;

abstract class AbstractObjectType implements ObjectTypeInterface
{
    public function description(): string
    {
        return '';
    }

    public function resolveField($value, $args, $context, ResolveInfo $info)
    {
        $method = 'get' . ucfirst($info->fieldName);

        return $value->$method();
    }

    public function create(ContainerInterface $container, string $name): ObjectType
    {
        return new ObjectType([
            'name' => $name,
            'description' => $this->description(),
            'fields' => function () use ($container) {
                return $this->fields($container);
            },
            'resolveField' => Closure::fromCallable([$this, 'resolveField']),
        ]);
    }
}
