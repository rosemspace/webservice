<?php

namespace Psrnext\GraphQL;

use Psr\Container\ContainerInterface;

interface TypeRegistryInterface extends ContainerInterface
{
    public function nonNull($typeInstance);

    public function listOf($typeInstance);
}
