<?php

namespace Rosem\Contract\Database;

interface SeederInterface
{
    public function run(SchemaInterface $schema): void;
}
