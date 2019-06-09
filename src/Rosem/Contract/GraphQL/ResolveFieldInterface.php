<?php

namespace Rosem\Contract\GraphQL;

interface ResolveFieldInterface
{
    public function resolveField($source, $args, ResolveInfoInterface $resolveInfo);
}
