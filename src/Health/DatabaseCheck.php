<?php

declare(strict_types=1);

namespace Tavp\Core\Health;

/**
 * Database health check.
 */
class DatabaseCheck
{
    public function __construct(private object $db)
    {
    }

    public function __invoke(): bool
    {
        try {
            $this->db->fetchOne('SELECT 1');
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
