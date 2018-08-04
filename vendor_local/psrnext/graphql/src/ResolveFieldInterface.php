<?php

namespace Psrnext\GraphQL;

interface ResolveFieldInterface
{
    public function resolveField($source, $args, ResolveInfoInterface $resolveInfo);
}
