<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('roles', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('name', 'string', ['size' => 100]));
            $table->add($schema->column('description', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });

        $schema->addIndex('roles', ['name'], 'roles_name_unique', true);
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('roles');
    }
};
