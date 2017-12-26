<?php

namespace True\GraphQL;

use Closure;
use GraphQL\Type\{
    Definition\ObjectType, Definition\Type, Schema, SchemaConfig
};
use TrueStandards\GraphQL\{
    GraphInterface, QueryInterface, MutationInterface
};

class Graph implements GraphInterface
{
    protected $types = [];
    protected $schemas = [];

    protected function initSchemaField(string $schema, string $field)
    {
        if (! isset($this->schemas[$schema])) {
            $this->schemas[$schema] = [$field => []];
        } else if (! isset($this->schemas[$schema][$field])) {
            $this->schemas[$schema][$field] = [];
        }
    }

    public function addType(string $name, Type $instance) { //TODO: lazy loading
        if (! isset($this->types[$name])) {
            $this->types[$name] = $instance;
        }
    }

    public function addQuery(QueryInterface $query, string $schema = 'default')
    {
        $this->initSchemaField($schema, 'queries');

        if (! isset($this->schemas[$schema]['queries'][$query->getName()])) {
            $this->schemas[$schema]['queries'][$query->getName()] = $query->toArray($this);
        }
    }

    public function addMutation(MutationInterface $mutation, string $schema = 'default')
    {
        $this->initSchemaField($schema, 'mutations');

        if (! isset($this->schemas[$schema]['mutations'][$mutation->getName()])) {
            $this->schemas[$schema]['mutations'][$mutation->getName()] = $mutation->toArray($this);
        }
    }

    public function getType(string $name)
    {
        return $this->types[$name];
    }

    protected function getQueryType(string $schema) : ObjectType
    {
        $this->initSchemaField($schema, 'queries');

        return new ObjectType([
            'name' => 'Query',
            'fields' => $this->schemas[$schema]['queries'],
        ]);
    }

    protected function getMutationType(string $schema) : ObjectType
    {
        $this->initSchemaField($schema, 'mutations');

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => $this->schemas[$schema]['mutations'],
        ]);
    }

    protected function getSubscriptionType(string $schema)
    {
        $this->initSchemaField($schema, 'subscriptions');

        return new ObjectType([
            'name' => 'Subscription',
            'fields' => $this->schemas[$schema]['subscriptions'],
        ]);
    }

    public function getSchema(string $schema = 'default')
    {
        return new Schema([
            'query' => $this->getQueryType($schema),
//            'mutation' => $this->getMutationType($schema),
//            'subscription' => $this->getSubscriptionType($schema),
            'typeLoader' => Closure::fromCallable([$this, 'getType']),
        ]);
    }
}
