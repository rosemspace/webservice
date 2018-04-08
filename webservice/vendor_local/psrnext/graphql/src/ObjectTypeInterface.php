<?php

namespace Psrnext\GraphQL;

use GraphQL\Type\Definition\{
    ObjectType, ResolveInfo
};
use Psr\Container\ContainerInterface;

interface ObjectTypeInterface extends NodeInterface
{
    public function fields(ContainerInterface $container): array;

    public function resolveField($value, $args, $context, ResolveInfo $info);
}
