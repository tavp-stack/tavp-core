<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('user_sessions', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('user_id', 'bigInteger'));
            $table->add($schema->column('token_hash', 'string', ['size' => 255]));
            $table->add($schema->column('type', 'string', ['size' => 20]));
            $table->add($schema->column('ip_address', 'string', ['size' => 45, 'null' => true]));
            $table->add($schema->column('user_agent', 'string', ['size' => 500, 'null' => true]));
            $table->add($schema->column('last_activity_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('expires_at', 'timestamp']));
            $table->add($schema->column('created_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('user_sessions');
    }
};
