<?php

namespace Rosem\Contract\GraphQL;

interface ComplexityInterface
{
    public function getComplexity($childrenComplexity, $args): int;
}
