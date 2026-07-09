<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Migrations;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Column;

/**
 * A readable schema builder for migrations.
 *
 * Each method maps directly to a Phalcon column definition, but uses
 * plain English names (string, integer, boolean...) so non-specialists
 * can read what a table will look like.
 */
class SchemaBuilder
{
    public function __construct(private AdapterInterface $connection)
    {
    }

    /**
     * Create a table with the given columns (built via the column() helper).
     */
    public function createTable(string $table, callable $definition): void
    {
        $columns = [];
        $definition(new TableDefinition($columns));

        $this->connection->createTable($table, null, [
            'columns' => $columns,
        ]);
    }

    /**
     * Drop a table if it exists.
     */
    public function dropTable(string $table): void
    {
        if ($this->connection->tableExists($table)) {
            $this->connection->dropTable($table);
        }
    }

    /**
     * Helper to build a column definition with readable modifiers.
     */
    public function column(string $name, string $type, array $options = []): array
    {
        $phalconType = match ($type) {
            'string' => Column::TYPE_VARCHAR,
            'text' => Column::TYPE_TEXT,
            'integer' => Column::TYPE_INTEGER,
            'bigInteger' => Column::TYPE_BIGINTEGER,
            'boolean' => Column::TYPE_BOOLEAN,
            'date' => Column::TYPE_DATE,
            'datetime' => Column::TYPE_DATETIME,
            'timestamp' => Column::TYPE_TIMESTAMP,
            'decimal' => Column::TYPE_DECIMAL,
            'json' => Column::TYPE_JSON,
            'enum' => Column::TYPE_CHAR,
            default => Column::TYPE_VARCHAR,
        };

        $columnOptions = array_merge([
            'type' => $phalconType,
            'name' => $name,
        ], $options);

        if (!isset($columnOptions['size']) && in_array($type, ['string', 'enum'], true)) {
            $columnOptions['size'] = 255;
        }

        return $columnOptions;
    }
}

/**
 * Collects column definitions inside a createTable() closure.
 */
class TableDefinition
{
    public function __construct(public array &$columns)
    {
    }

    public function add(array $column): void
    {
        $this->columns[] = $column;
    }
}
