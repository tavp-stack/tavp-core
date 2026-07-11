<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Migrations;

use PDO;

/**
 * Simple SQL-based schema builder using PDO.
 *
 * Alternative to Phalcon SchemaBuilder — works with any PDO connection.
 */
class SqlSchema
{
    public function __construct(private PDO $pdo)
    {
    }

    public function createTable(string $table, callable $definition): void
    {
        $columns = [];
        $references = [];
        $def = new TableDefinition($columns, $references);
        $definition($def);

        $colDefs = [];
        foreach ($columns as $col) {
            $parts = ['`' . $col['name'] . '`'];

            $type = $col['type'];
            $size = $col['size'] ?? null;

            $sqlType = match ($type) {
                'string' => $size ? "VARCHAR({$size})" : 'VARCHAR(255)',
                'text' => 'LONGTEXT',
                'integer' => 'INT',
                'bigInteger' => 'BIGINT',
                'boolean' => 'TINYINT(1)',
                'date' => 'DATE',
                'datetime' => 'DATETIME',
                'timestamp' => 'TIMESTAMP',
                'decimal' => 'DECIMAL(10,2)',
                'json' => 'JSON',
                default => 'VARCHAR(255)',
            };

            $parts[] = $sqlType;

            if (!($col['null'] ?? false)) {
                $parts[] = 'NOT NULL';
            }

            if (isset($col['default'])) {
                $default = $col['default'];
                $parts[] = "DEFAULT " . (is_string($default) ? "'{$default}'" : $default);
            }

            if ($col['identity'] ?? false) {
                $parts[] = 'AUTO_INCREMENT';
            }

            if ($col['primary'] ?? false) {
                $parts[] = 'PRIMARY KEY';
            }

            $colDefs[] = implode(' ', $parts);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (\n  " . implode(",\n  ", $colDefs) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->pdo->exec($sql);
    }

    public function dropTable(string $table): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS `{$table}`");
    }

    public function addColumn(string $table, string $column, string $type, array $options = []): void
    {
        $sqlType = match ($type) {
            'string' => isset($options['size']) ? "VARCHAR({$options['size']})" : 'VARCHAR(255)',
            'text' => 'LONGTEXT',
            'integer' => 'INT',
            'bigInteger' => 'BIGINT',
            'boolean' => 'TINYINT(1)',
            'timestamp' => 'TIMESTAMP',
            default => 'VARCHAR(255)',
        };

        $sql = "ALTER TABLE `{$table}` ADD `{$column}` {$sqlType}";
        $this->pdo->exec($sql);
    }

    public function dropColumn(string $table, string $column): void
    {
        $this->pdo->exec("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
    }

    /**
     * Helper to build a column definition.
     */
    public function column(string $name, string $type, array $options = []): array
    {
        return array_merge([
            'name' => $name,
            'type' => $type,
        ], $options);
    }

    /**
     * Add an index to a table.
     */
    public function addIndex(string $table, array $columns, ?string $name = null, bool $unique = false): void
    {
        $indexName = $name ?: 'idx_' . $table . '_' . implode('_', $columns);
        $uniqueStr = $unique ? 'UNIQUE ' : '';
        $cols = implode(', ', array_map(fn ($c) => "`{$c}`", $columns));

        $this->pdo->exec("CREATE {$uniqueStr}INDEX `{$indexName}` ON `{$table}` ({$cols})");
    }

    /**
     * Drop an index from a table.
     */
    public function dropIndex(string $table, string $indexName): void
    {
        $this->pdo->exec("DROP INDEX `{$indexName}` ON `{$table}`");
    }

    /**
     * Add a foreign key constraint.
     */
    public function addForeignKey(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete = 'RESTRICT',
        string $onUpdate = 'RESTRICT'
    ): void {
        $fkName = "fk_{$table}_{$column}";
        $sql = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$column}`) REFERENCES `{$referencedTable}`(`{$referencedColumn}`) ON DELETE {$onDelete} ON UPDATE {$onUpdate}";
        $this->pdo->exec($sql);
    }

    /**
     * Drop a foreign key constraint.
     */
    public function dropForeignKey(string $table, string $constraintName): void
    {
        $this->pdo->exec("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraintName}`");
    }
}
