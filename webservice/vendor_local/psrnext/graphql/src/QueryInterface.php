<?php

namespace Psrnext\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;

interface QueryInterface extends NodeInterface
{
    public function type(ContainerInterface $container);

    public function args(): array;

    public function resolve($source, $args, $context, ResolveInfo $resolveInfo);
}
