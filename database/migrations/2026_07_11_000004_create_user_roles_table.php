<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('user_roles', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('user_id', 'bigInteger'));
            $table->add($schema->column('role_id', 'bigInteger'));
        });

        $schema->addIndex('user_roles', ['user_id', 'role_id'], 'user_roles_unique', true);
        $schema->addForeignKey('user_roles', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $schema->addForeignKey('user_roles', 'role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('user_roles');
    }
};
