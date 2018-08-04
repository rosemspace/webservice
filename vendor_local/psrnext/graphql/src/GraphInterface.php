<?php

namespace Psrnext\GraphQL;

interface GraphInterface
{
    public function addSchema(string $name, SchemaInterface $schema): void;

    public function getSchema(string $name): SchemaInterface;
}
