<?php

namespace Rosem\Psr\Database;

interface DatabaseProviderInterface
{
    public function getMigrations(): array;

    public function getSeeders(): array;
}
