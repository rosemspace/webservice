<?php

namespace Rosem\Psr\GraphQL;

interface ResolveFieldInterface
{
    public function resolveField($source, $args, ResolveInfoInterface $resolveInfo);
}
