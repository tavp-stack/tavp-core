<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('role_permissions', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('role_id', 'bigInteger'));
            $table->add($schema->column('permission_id', 'bigInteger'));
        });

        $schema->addIndex('role_permissions', ['role_id', 'permission_id'], 'role_permissions_unique', true);
        $schema->addForeignKey('role_permissions', 'role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $schema->addForeignKey('role_permissions', 'permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('role_permissions');
    }
};
