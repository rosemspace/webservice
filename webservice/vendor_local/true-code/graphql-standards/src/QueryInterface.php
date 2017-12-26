<?php

namespace TrueStandards\GraphQL;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;

interface QueryInterface
{
    public function getName() : string;

    public function getDescription() : string;

    public function type();

    public function args() : array;

    public function resolve($source, $args, $context, ResolveInfo $resolveInfo);

    public function toArray() : array;
}
