<?php

namespace Psrnext\GraphQL;

interface ComplexityInterface
{
    public function getComplexity($childrenComplexity, $args): int;
}
