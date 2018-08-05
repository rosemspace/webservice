<?php

namespace Rosem\Psr\GraphQL;

abstract class AbstractObjectType extends AbstractNode implements ObjectTypeInterface
{
    protected const TYPE_REGEX = '~^.*\\\|Type$~';

    abstract public function getBaseFields(TypeRegistryInterface $registry);

    public static function field($type, string $description = ''): array
    {
        return [
            'type' => $type,
            'description' => $description,
        ];
    }

    public function getName(): string
    {
        return preg_replace(self::TYPE_REGEX, '', static::class);
    }

    public function addFields(\Closure $fieldFactory): void
    {
        $this->factories[] = $fieldFactory;
    }

    public function getFields(TypeRegistryInterface $registry): array {
        $fields = $this->getBaseFields($registry);

        foreach ($this->factories as $factory) {
            $fields += $factory($registry);
        }

        return $fields;
    }
}
