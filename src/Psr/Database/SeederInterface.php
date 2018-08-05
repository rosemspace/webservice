<?php

namespace Rosem\Psr\Database;

interface SeederInterface
{
    public function run(SchemaInterface $schema): void;
}
