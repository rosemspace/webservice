<?php

namespace Rosem\Component\GraphQL;

use Rosem\Contract\GraphQL\{
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

    public function getSchema(string $name = 'default'): SchemaInterface
    {
        return $this->schemas[$name];
    }
}
