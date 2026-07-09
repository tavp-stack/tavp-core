<?php

declare(strict_types=1);

namespace Tavp\Core\Logging;

/**
 * Structured logger writing JSON lines with a request id.
 *
 * Drivers: file, stdout. Sentry/remote drivers added in production.
 */
class Logger
{
    private string $requestId;

    public function __construct(
        private string $driver = 'file',
        private string $path = '',
        ?string $requestId = null,
    ) {
        $this->requestId = $requestId ?? bin2hex(random_bytes(8));
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $entry = json_encode([
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'request_id' => $this->requestId,
            'time' => date('c'),
        ]);

        if ($this->driver === 'stdout') {
            fwrite(STDOUT, $entry . "\n");

            return;
        }

        $logPath = $this->path !== ''
            ? $this->path
            : (function_exists('storage_path') ? storage_path('logs/app.log') : sys_get_temp_dir() . '/tavp-app.log');
        file_put_contents($logPath, $entry . "\n", FILE_APPEND);
    }
}
