<?php

namespace Rosem\GraphQL;

use Psrnext\GraphQL\{
    GraphInterface, SchemaInterface
};

class Graph implements GraphInterface
{
    /**
     * @var SchemaInterface[]
     */
    protected $schemas = [];

    public function addSchema(string $name, SchemaInterface $schema): void
    {
        $this->schemas[$name] = $schema;
    }

    public function schema(string $name = 'default'): SchemaInterface
    {
        return $this->schemas[$name];
    }
}
