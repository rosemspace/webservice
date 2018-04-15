<?php

namespace Psrnext\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;

interface QueryInterface extends NodeInterface
{
    public function getType(TypeRegistryInterface $typeRegistry);

    public function getArguments(): array;

    public function resolve($source, $args, $context, ResolveInfo $resolveInfo);
}
