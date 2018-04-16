<?php

namespace Psrnext\GraphQL;

abstract class AbstractQuery extends AbstractNode implements QueryInterface
{
    abstract public function getDefaultArguments(TypeRegistryInterface $typeRegistry);

    public function addArguments(\Closure $argumentFactory): void
    {
        $this->factories[] = $argumentFactory;
    }

    public function getArguments(TypeRegistryInterface $typeRegistry): array {
        $fields = $this->getDefaultArguments($typeRegistry);

        foreach ($this->factories as $factory) {
            $fields += $factory($typeRegistry);
        }

        return $fields;
    }
}
