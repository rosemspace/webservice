<?php

namespace TrueStandards\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

interface GraphInterface
{
    public function addType(string $name, Type $instance);

    public function getType(string $name);
}
