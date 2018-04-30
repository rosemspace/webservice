<?php

namespace Psrnext\GraphQL;

interface GraphInterface
{
    public function addSchema(string $name, SchemaInterface $schema): void;

    public function schema(string $name = 'default'): SchemaInterface;
}
