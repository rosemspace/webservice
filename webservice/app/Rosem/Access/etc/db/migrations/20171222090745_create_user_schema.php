<?php


use Phinx\Migration\AbstractMigration;

class CreateUserSchema extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('roles')
            ->addColumn('name', 'string')->addIndex('name', ['unique' => true])
            ->create();

        $this->table('permissions')
            ->addColumn('label', 'string')->addIndex('label', ['unique' => true])
            ->create();

        $this->table('users')
            ->addColumn('first_name', 'string')
            ->addColumn('last_name', 'string')
            ->addColumn('email', 'string')->addIndex('email', ['unique' => true])
            ->addColumn('password', 'string', ['limit' => 60])
            // rememberToken
            ->addColumn('remember_token', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('role_id', 'integer')->addForeignKey('role_id', 'roles')
            ->addTimestamps()
            // softDeletes
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->create();

        $this->table('role_permission')
            ->addColumn('role_id', 'integer')
            ->addForeignKey('role_id', 'roles')
            ->addColumn('permission_id', 'integer')
            ->addForeignKey('permission_id', 'permissions')
            ->create();

        $this->table('user_permission')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users')
            ->addColumn('permission_id', 'integer')
            ->addForeignKey('permission_id', 'permissions')
            ->create();
    }
}
