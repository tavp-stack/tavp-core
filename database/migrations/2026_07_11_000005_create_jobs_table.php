<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('jobs', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('queue', 'string', ['size' => 255]));
            $table->add($schema->column('job_class', 'string', ['size' => 255]));
            $table->add($schema->column('payload', 'text'));
            $table->add($schema->column('attempts', 'integer'));
            $table->add($schema->column('reserved_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('available_at', 'timestamp'));
            $table->add($schema->column('failed_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('exception', 'text', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
        });

        $schema->addIndex('jobs', ['queue', 'reserved_at', 'available_at'], 'jobs_queue_index');
        $schema->addIndex('jobs', ['queue', 'reserved_at', 'attempts'], 'jobs_processing_index');
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('jobs');
    }
};
