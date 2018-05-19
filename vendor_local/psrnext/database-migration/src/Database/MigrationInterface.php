<?php

namespace Psrnext\Database;

interface MigrationInterface
{
    public function change(SchemaInterface $schema): void;

    public function up(SchemaInterface $schema): void;

    public function down(SchemaInterface $schema): void;
}
