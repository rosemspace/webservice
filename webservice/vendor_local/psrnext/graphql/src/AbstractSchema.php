<?php

namespace Psrnext\GraphQL;

abstract class AbstractSchema implements SchemaInterface
{
    public function query(string $name, callable $queryFactory): void
    {
        $this->addNode(self::NODE_TYPE_QUERY, $name, $queryFactory);
    }

    public function mutation(string $name, callable $mutationFactory): void
    {
        $this->addNode(self::NODE_TYPE_MUTATION, $name, $mutationFactory);
    }

    public function subscription(string $name, callable $subscriptionFactory): void
    {
        $this->addNode(self::NODE_TYPE_SUBSCRIPTION, $name, $subscriptionFactory);
    }
}
