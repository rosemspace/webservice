<?php

namespace Psrnext\GraphQL;

interface SchemaInterface
{
    public function addNode(string $type, string $name, callable $nodeFactory): void;

    public function query(string $name, callable $queryFactory): void;

    public function mutation(string $name, callable $mutationFactory): void;

    public function subscription(string $name, callable $subscriptionFactory): void;

    public function getTree(): array;
}
