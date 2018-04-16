<?php

namespace Psrnext\GraphQL;

abstract class AbstractObjectType extends AbstractNode implements ObjectTypeInterface
{
    abstract public function getDefaultFields(TypeRegistryInterface $typeRegistry);

    public function addFields(\Closure $fieldFactory): void
    {
        $this->factories[] = $fieldFactory;
    }

    public function getFields(TypeRegistryInterface $typeRegistry): array {
        $fields = $this->getDefaultFields($typeRegistry);

        foreach ($this->factories as $factory) {
            $fields += $factory($typeRegistry);
        }

        return $fields;
    }
}
