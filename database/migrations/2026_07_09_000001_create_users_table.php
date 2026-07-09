<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('users', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('name', 'string', ['size' => 255]));
            $table->add($schema->column('email', 'string', ['size' => 255]));
            $table->add($schema->column('phone', 'string', ['size' => 20, 'null' => true]));
            $table->add($schema->column('password_hash', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('avatar_url', 'string', ['size' => 500, 'null' => true]));
            $table->add($schema->column('email_verified_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('phone_verified_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('status', 'string', ['size' => 20]));
            $table->add($schema->column('role', 'string', ['size' => 20]));
            $table->add($schema->column('last_login_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('settings', 'json', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('users');
    }
};
