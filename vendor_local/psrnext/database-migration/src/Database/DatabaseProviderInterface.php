<?php

namespace Psrnext\Database;

interface DatabaseProviderInterface
{
    public function getMigrations(): array;

    public function getSeeders(): array;
}
