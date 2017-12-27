<?php

namespace TrueStandards\GraphQL;

use GraphQL\Type\Definition\Type;

interface GraphInterface
{
    public function addType(string $name, Type $instance): void;

    public function getType(string $name);

    public function addQuery(QueryInterface $query, string $schema = 'default'): void;
}
