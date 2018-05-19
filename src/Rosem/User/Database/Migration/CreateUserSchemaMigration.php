<?php

namespace Rosem\User\Database\Migration;

use Psrnext\Database\AbstractMigration;
use Psrnext\Database\SchemaInterface;

final class CreateUserSchemaMigration extends AbstractMigration
{
    public function change(SchemaInterface $schema): void
    {
        $schema->table('users')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->create();
    }
}
