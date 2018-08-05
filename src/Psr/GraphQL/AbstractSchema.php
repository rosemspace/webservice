<?php

namespace Rosem\Psr\GraphQL;

abstract class AbstractSchema implements SchemaInterface
{
    public function query(string $name, callable $queryFactory): void
    {
        $this->addNode(NodeType::QUERY, $name, $queryFactory);
    }

    public function mutation(string $name, callable $mutationFactory): void
    {
        $this->addNode(NodeType::MUTATION, $name, $mutationFactory);
    }

    public function subscription(string $name, callable $subscriptionFactory): void
    {
        $this->addNode(NodeType::SUBSCRIPTION, $name, $subscriptionFactory);
    }
}
