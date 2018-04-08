<?php

namespace Psrnext\GraphQL;

use Psr\Container\ContainerInterface;

interface NodeInterface
{
    public function description(): string;

    public function create(ContainerInterface $container, string $name);
}
