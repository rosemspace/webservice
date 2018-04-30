<?php

namespace Psrnext\GraphQL;

abstract class AbstractObjectType extends AbstractNode implements ObjectTypeInterface
{
    protected const TYPE_REGEX = '~^.*\\\|Type$~';

    abstract public function getBaseFields(TypeRegistryInterface $typeRegistry);

    public function getName(): string
    {
        return preg_replace(self::TYPE_REGEX, '', static::class);
    }

    public function addFields(\Closure $fieldFactory): void
    {
        $this->factories[] = $fieldFactory;
    }

    public function getFields(TypeRegistryInterface $typeRegistry): array {
        $fields = $this->getBaseFields($typeRegistry);

        foreach ($this->factories as $factory) {
            $fields += $factory($typeRegistry);
        }

        return $fields;
    }
}
