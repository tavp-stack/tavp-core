<?php

declare(strict_types=1);

namespace Tavp\Core\Broadcasting;

/**
 * Log broadcast driver for development.
 */
class LogBroadcastDriver implements BroadcastDriver
{
    public function broadcast(string $channel, string $event, array $data): bool
    {
        $logDir = storage_path('logs/broadcast');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/' . date('Y-m-d') . '.log';
        $entry = date('Y-m-d H:i:s') . " [{$channel}] {$event}: " . json_encode($data) . "\n";

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
        return true;
    }
}
