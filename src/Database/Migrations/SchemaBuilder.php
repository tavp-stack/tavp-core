<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Migrations;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Column;
use Phalcon\Db\Reference;
use Phalcon\Db\ReferenceInterface;

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
        $references = [];
        $def = new TableDefinition($columns, $references);
        $definition($def);

        $options = ['columns' => $columns];

        if (!empty($references)) {
            $options['references'] = $references;
        }

        $this->connection->createTable($table, null, $options);
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
     * Add a foreign key constraint to an existing table.
     */
    public function addForeignKey(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete = 'RESTRICT',
        string $onUpdate = 'RESTRICT'
    ): void {
        $reference = new Reference([
            'referencedTable' => $referencedTable,
            'columns' => [$column],
            'referencedColumns' => [$referencedColumn],
            'onDelete' => $onDelete,
            'onUpdate' => $onUpdate,
        ]);

        $this->connection->addForeignKey($table, null, $reference);
    }

    /**
     * Drop a foreign key constraint.
     */
    public function dropForeignKey(string $table, string $constraintName): void
    {
        $this->connection->dropForeignKey($table, null, $constraintName);
    }

    /**
     * Add an index to a table.
     */
    public function addIndex(string $table, array $columns, ?string $name = null, bool $unique = false): void
    {
        $indexName = $name ?: 'idx_' . $table . '_' . implode('_', $columns);

        $this->connection->addIndex($table, null, [
            'fields' => $columns,
            'name' => $indexName,
            'unique' => $unique,
        ]);
    }

    /**
     * Drop an index from a table.
     */
    public function dropIndex(string $table, string $indexName): void
    {
        $this->connection->dropIndex($table, null, $indexName);
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
 * Collects column definitions and foreign keys inside a createTable() closure.
 */
class TableDefinition
{
    public function __construct(
        public array &$columns,
        public array &$references
    ) {
    }

    public function add(array $column): void
    {
        $this->columns[] = $column;
    }

    /**
     * Add a foreign key constraint inline.
     */
    public function foreignKey(
        string $column,
        string $referencedTable,
        string $referencedColumn = 'id',
        string $onDelete = 'RESTRICT',
        string $onUpdate = 'RESTRICT'
    ): void {
        $this->references[] = new Reference([
            'referencedTable' => $referencedTable,
            'columns' => [$column],
            'referencedColumns' => [$referencedColumn],
            'onDelete' => $onDelete,
            'onUpdate' => $onUpdate,
        ]);
    }
}
