<?php

namespace TrueStandards\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;

interface ObjectTypeInterface
{
    public function fields() : array;

    public function resolveField($value, $args, $context, ResolveInfo $info);
}
