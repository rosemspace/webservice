<?php

namespace Rosem\Contract\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;

interface QueryInterface extends DescriptionInterface
{
    public function getType(TypeRegistryInterface $typeRegistry);

    public function getArguments(TypeRegistryInterface $typeRegistry): array;

    public function addArguments(\Closure $argumentFactory): void;

    public function resolve($source, $args, $context, ResolveInfo $resolveInfo);
}
