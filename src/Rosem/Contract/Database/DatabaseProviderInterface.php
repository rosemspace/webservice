<?php

namespace Rosem\Contract\Database;

interface DatabaseProviderInterface
{
    public function getMigrations(): array;

    public function getSeeders(): array;
}
