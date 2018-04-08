<?php

namespace Psrnext\GraphQL;

use Psr\Container\ContainerInterface;

abstract class AbstractQuery implements QueryInterface
{
    public function description(): string
    {
        return '';
    }

    public function args(): array
    {
        return [];
    }

    public function create(ContainerInterface $container, string $name): array
    {
        return [
            'name' => $name,
            'description' => $this->description(),
            'type' => $this->type($container),
            'args' => $this->args(),
            'resolve' => \Closure::fromCallable([$this, 'resolve']),
        ];
    }
}
