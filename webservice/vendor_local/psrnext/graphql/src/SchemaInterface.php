<?php

namespace Psrnext\GraphQL;

interface SchemaInterface
{
    public const NODE_TYPE_QUERY = 'Query';

    public const NODE_TYPE_MUTATION = 'Mutation';

    public const NODE_TYPE_SUBSCRIPTION = 'Subscription';

    public function addNode(string $type, string $name, callable $nodeFactory): void;

    public function query(string $name, callable $queryFactory): void;

    public function mutation(string $name, callable $mutationFactory): void;

    public function subscription(string $name, callable $subscriptionFactory): void;

    public function getQueryData(): ?array;

    public function getMutationData(): ?array;

    public function getSubscriptionData(): ?array;
}
