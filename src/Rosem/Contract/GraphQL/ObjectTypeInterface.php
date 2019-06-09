<?php

namespace Rosem\Contract\GraphQL;

interface ObjectTypeInterface extends DescriptionInterface
{
    public function getName(): string;

    public function getFields(TypeRegistryInterface $typeRegistry): array;

    public function addFields(\Closure $factory): void;
}
