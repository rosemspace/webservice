<?php

namespace Psrnext\GraphQL;

interface ObjectTypeInterface extends NodeInterface
{
    public function getName(): string;

    public function getFields(TypeRegistryInterface $typeRegistry): array;

//    public function addField(string $name, string $type, string $description): void;
}
