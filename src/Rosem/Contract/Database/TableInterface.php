<?php

namespace Rosem\Contract\Database;

interface TableInterface //TODO
{
    public function renameTo(string $name): self;

    public function addColumn(string $name, string $type, array $options): self;

    public function addIndex($columns, array $options): self;

    public function addTimestamps();

    public function create(): bool;

    public function update(): bool;

    public function save(): bool;

    public function truncate(): bool;

    public function drop(): bool;

    public function dropIfExists(): bool;
}
