<?php

namespace Rosem\User\Provider;

use Rosem\Psr\Database\DatabaseProviderInterface;
use Rosem\User\Database\Migration\CreateUserSchemaMigration;

class DatabaseProvider implements DatabaseProviderInterface
{
    public function getMigrations(): array
    {
        return [
            '20171222090745_create_user_schema' => CreateUserSchemaMigration::class,
        ];
    }

    public function getSeeders(): array
    {
        return [];
    }
}
