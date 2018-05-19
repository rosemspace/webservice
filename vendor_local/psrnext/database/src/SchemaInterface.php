<?php

namespace Psrnext\Database;

interface SchemaInterface
{
    public function table(string $name): TableInterface;

    public function hasTable(string $name): bool;
}
