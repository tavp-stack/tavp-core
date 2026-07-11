<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Migrations;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Base class for all database migrations.
 *
 * Subclasses implement up() to create schema and down() to revert it,
 * exactly like Laravel migrations. The schema builder exposes a
 * readable, column-by-column API.
 */
abstract class Migration
{
    abstract public function up(SchemaBuilder|SqlSchema $schema): void;

    abstract public function down(SchemaBuilder|SqlSchema $schema): void;

    /**
     * Run the "up" migration against the given connection.
     */
    final public function runUp(AdapterInterface $connection): void
    {
        $this->up(new SchemaBuilder($connection));
    }

    /**
     * Run the "down" migration against the given connection.
     */
    final public function runDown(AdapterInterface $connection): void
    {
        $this->down(new SchemaBuilder($connection));
    }
}
