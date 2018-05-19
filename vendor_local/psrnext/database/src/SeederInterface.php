<?php

namespace Psrnext\Database;

interface SeederInterface
{
    public function run(SchemaInterface $schema): void;
}
