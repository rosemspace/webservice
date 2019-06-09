<?php

namespace Rosem\Contract\GraphQL;

use Psr\Container\ContainerInterface;

interface TypeRegistryInterface extends ContainerInterface
{
    public function boolean();

    public function id();

    public function int();

    public function float();

    public function string();

    public function nonNull($typeInstance);

    public function listOf($typeInstance);
}
