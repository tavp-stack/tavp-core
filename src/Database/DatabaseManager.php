<?php

declare(strict_types=1);

namespace Tavp\Core\Database;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Adapter\PdoFactory;

/**
 * Creates and manages the database connection.
 *
 * Reads config from config/database.php, creates the appropriate
 * Phalcon adapter (MySQL, PostgreSQL, or SQLite), and provides
 * it to the rest of the application.
 */
class DatabaseManager
{
    private ?AdapterInterface $adapter = null;

    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the active database adapter, creating it if needed.
     */
    public function getAdapter(): AdapterInterface
    {
        if ($this->adapter === null) {
            $this->adapter = $this->createAdapter();
        }

        return $this->adapter;
    }

    /**
     * Create a new Phalcon adapter from the active connection config.
     */
    private function createAdapter(): AdapterInterface
    {
        $name = $this->config['default'] ?? 'mysql';
        $connections = $this->config['connections'] ?? [];

        if (!isset($connections[$name])) {
            throw new \RuntimeException(
                "Database connection '{$name}' is not defined in config/database.php."
            );
        }

        $connection = $connections[$name];

        $adapterClass = match ($connection['adapter'] ?? 'Mysql') {
            'Mysql' => \Phalcon\Db\Adapter\Pdo\Mysql::class,
            'Postgresql' => \Phalcon\Db\Adapter\Pdo\Postgresql::class,
            'Sqlite' => \Phalcon\Db\Adapter\Pdo\Sqlite::class,
            default => throw new \RuntimeException(
                "Unsupported database adapter: {$connection['adapter']}"
            ),
        };

        $options = $this->buildOptions($connection);

        return new $adapterClass($options);
    }

    /**
     * Build the PDO options array for the adapter.
     */
    private function buildOptions(array $connection): array
    {
        $adapter = $connection['adapter'] ?? 'Mysql';

        if ($adapter === 'Sqlite') {
            return [
                'dbname' => $connection['dbname'] ?? 'storage/tavp.sqlite',
            ];
        }

        $options = [
            'host' => $connection['host'] ?? '127.0.0.1',
            'port' => $connection['port'] ?? 3306,
            'dbname' => $connection['dbname'] ?? 'tavp',
            'username' => $connection['username'] ?? '',
            'password' => $connection['password'] ?? '',
        ];

        if (isset($connection['charset'])) {
            $options['options'][\PDO::MYSQL_ATTR_INIT_COMMAND] =
                "SET NAMES {$connection['charset']}";
        }

        return $options;
    }

    /**
     * Close the active connection.
     */
    public function close(): void
    {
        if ($this->adapter !== null) {
            $this->adapter->close();
            $this->adapter = null;
        }
    }
}
