<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('otp_codes', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('identifier', 'string', ['size' => 255]));
            $table->add($schema->column('code', 'string', ['size' => 10]));
            $table->add($schema->column('channel', 'string', ['size' => 20]));
            $table->add($schema->column('attempts', 'integer', ['default' => 0]));
            $table->add($schema->column('verified_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('expires_at', 'timestamp']));
            $table->add($schema->column('created_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('otp_codes');
    }
};
