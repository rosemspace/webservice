<?php

namespace Rosem\Psr\GraphQL;

interface ComplexityInterface
{
    public function getComplexity($childrenComplexity, $args): int;
}
